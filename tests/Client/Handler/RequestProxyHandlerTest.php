<?php
namespace Spike\Tests\Client\Handler;

use Spike\Client\Handler\RequestProxyHandler;
use Spike\Exception\InvalidArgumentException;
use Spike\Tests\TestCase;
use Spike\Protocol\Spike;
use Spike\Client\EventStore;
use Slince\Event\Event;

class RequestProxyHandlerTest extends TestCase
{
    public function testHandle()
    {
        $client = $this->getClientStub();
        $handler = new RequestProxyHandler($client, $this->getConnectionMock());
        $message = new Spike('request_proxy', ['serverPort' => 8086, 'error' => 'foo'], [
            'Proxy-Connection-ID' => 1
        ]);
        $client->getDispatcher()->addListener(EventStore::REQUEST_PROXY, function(Event $event) use ($message){
            $this->assertEquals('8086', $event->getArgument('tunnel')->getServerPort());
            $this->assertNotNull($event->getArgument('client'));
        });
        $handler->handle($message);
        $this->assertNull($client->getClientId());
        $this->expectException(InvalidArgumentException::class);
        $handler->handle(new Spike('request_proxy', ['serverPort' => 808888, 'error' => 'foo'], [
            'Proxy-Connection-ID' => 1
        ]));
    }
}