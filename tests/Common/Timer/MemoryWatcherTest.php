<?php
namespace Spike\Tests\Common\Timer;

use Spike\Common\Timer\MemoryWatcher;

class MemoryWatcherTest extends TestCase
{
    public function testConstruct()
    {
        $watcher = new MemoryWatcher($this->getLoggerStub());
        $this->assertEquals(60, $watcher->getInterval());
    }

    public function testExecute()
    {
        $logger = $this->getLoggerStub();
        $timer = $this->getMockBuilder(MemoryWatcher::class)
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
        $this->loop->run();
        $stream = $logger->getOutput()->getStream();
        fseek($stream, 0);
        $this->assertContains('Memory usage', stream_get_contents($stream));
        $this->assertContains('Memory usage', file_get_contents($logger->getFile()));
    }
}