<?php
namespace Spike\Tests\Server\Timer;

use Spike\Server\Timer\SummaryWatcher;
use Spike\Tests\Common\Timer\TestCase;
use Spike\Tests\Common\Timer\CallableTimer;

class SummaryWatcherTest extends TestCase
{
    public function testGetter()
    {
        $server = $this->getServerMock();
        $timer = new SummaryWatcher($server);
        $this->assertEquals($server, $timer->getServer());

        $this->assertGreaterThan(0, $timer->getInterval());
    }

    public function testConstruct()
    {
        $server = $this->getServerStub();
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
            $this->cancelTimer($timer);
            $this->getEventLoop()->stop();
        }));
        $this->getEventLoop()->run();
        $this->assertContains('Client Total: 0', file_get_contents($logger->getFile()));
    }
}