<?php
namespace Spike\Tests\Server\Timer;

use Spike\Server\Client;
use Spike\Server\Timer\ClientScanTimer;
use Spike\Tests\Common\Timer\TestCase;
use Spike\Tests\Common\Timer\CallableTimer;

class ClientScanTimerTest extends TestCase
{
    public function testGetter()
    {
        $server = $this->getServerMock();
        $timer = new ClientScanTimer($server);
        $this->assertEquals($server, $timer->getServer());

        $this->assertGreaterThan(0, $timer->getInterval());
    }

    public function testConstruct()
    {
        $server = $this->getServerMock();
        $timer = $this->getMockBuilder(ClientScanTimer::class)
            ->setMethods(['getInterval'])
            ->setConstructorArgs([
                $server
            ])
            ->getMock();
        $timer->method('getInterval')->willReturn(0.1);
        $client = new Client([], $this->getConnectionMock());
        $server->getClients()->add($client);
        $this->assertGreaterThan(0, count($server->getClients()));
        $client->setActiveAt(new \DateTime('-5 hours'));
        $this->addTimer($timer);

        $this->addTimer(new CallableTimer(0.2, function() use ($timer){
            $this->cancelTimer($timer);
        }));
        $this->getEventLoop()->run();
        $this->assertCount(0, $server->getClients());
    }
}