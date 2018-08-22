<?php
namespace Spike\Tests\Common\Timer;

use Spike\Common\Timer\MemoryWatchTimer;

class MemoryWatchTimerTest extends TestCase
{
    public function testConstruct()
    {
        $watcher = new MemoryWatchTimer($this->getLoggerStub());
        $this->assertEquals(60, $watcher->getInterval());
    }

    public function testExecute()
    {
        $logger = $this->getLoggerStub();
        $timer = $this->getMockBuilder(MemoryWatchTimer::class)
            ->setMethods(['getInterval'])
            ->setConstructorArgs([$logger])
            ->getMock();
        $timer->method('getInterval')->willReturn(0.1);

        $this->addTimer($timer);
        $callableTimer = new CallableTimer(0.2, function() use ($timer, &$callableTimer){
            $this->cancelTimer($timer);
            $this->cancelTimer($callableTimer);
            $this->loop->stop();
        });

        $this->addTimer($callableTimer);
        $stream = $logger->getOutput()->getStream();

        $this->getEventLoop()->addWriteStream($stream, function($stream) use ($logger){
            fseek($stream, 0);
            $this->assertContains('Memory usage', stream_get_contents($stream));
            $this->assertContains('Memory usage', file_get_contents($logger->getFile()));
            $this->getEventLoop()->removeWriteStream($stream);
        });

        $this->loop->run();
    }
}