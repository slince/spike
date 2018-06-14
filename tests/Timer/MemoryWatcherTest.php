<?php
namespace Spike\Tests\Timer;

use Spike\Logger\Logger;
use Spike\Timer\CallableTimer;
use Spike\Timer\MemoryWatcher;
use Symfony\Component\Console\Output\StreamOutput;

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
        $this->addTimer(new CallableTimer(0.2, function() use ($timer){
            $timer->cancel();
        }));
        $this->loop->run();
        $stream = $logger->getOutput()->getStream();
        fseek($stream, 0);
        $this->assertContains('Memory usage', stream_get_contents($stream));
        $this->assertContains('Memory usage', file_get_contents($logger->getFile()));
    }
}