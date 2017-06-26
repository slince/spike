<?php
namespace Spike\Tests\Server\Timer;

use Spike\Server\Client;
use Spike\Server\Timer\ReviewClient;
use Spike\Tests\Timer\TestCase;
use Spike\Timer\CallableTimer;

class ReviewClientTest extends TestCase
{
    public function testGetter()
    {
        $server = $this->getServerMock();
        $timer = new ReviewClient($server);
        $this->assertEquals($server, $timer->getServer());

        $this->assertGreaterThan(0, $timer->getInterval());
    }

    public function testConstruct()
    {
        $server = $this->getServerMock();
        $timer = $this->getMockBuilder(ReviewClient::class)
            ->setMethods(['getInterval'])
            ->setConstructorArgs([
                $server
            ])
            ->getMock();
        $timer->method('getInterval')->willReturn(0.1);
        $client = new Client([], $this->getConnectionMock());
        $server->getClients()->add($client);
        $this->assertGreaterThan(0, count($server->getClients()));
        $client->setLastActiveAt(strtotime('-5 hours'));
        $this->addTimer($timer);
        $this->addTimer(new CallableTimer(0.2, function() use ($timer){
            $timer->cancel();
        }));
        $this->getLoop()->run();
        $this->assertCount(0, $server->getClients());
    }
}