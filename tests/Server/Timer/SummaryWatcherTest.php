<?php
namespace Spike\Tests\Server\Timer;

use Spike\Server\Timer\SummaryWatcher;
use Spike\Tests\Timer\TestCase;
use Spike\Timer\CallableTimer;

class SummaryWatcherTest extends TestCase
{
    public function testConstruct()
    {
        $server = $this->getServerMock();
        $logger = $this->getLoggerStub();
        $server->setLogger($logger);
        $timer = $this->getMockBuilder(SummaryWatcher::class)
            ->setMethods(['getInterval'])
            ->setConstructorArgs([
                $server
            ])
            ->getMock();
        $timer->method('getInterval')->willReturn(0.1);
        $this->addTimer($timer);
        $this->addTimer(new CallableTimer(0.2, function() use ($timer){
            $timer->cancel();
        }));
        $this->getLoop()->run();
        $this->assertContains('Client Total: 0', file_get_contents($logger->getFile()));
    }
}