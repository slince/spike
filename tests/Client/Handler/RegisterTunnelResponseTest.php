<?php
namespace Spike\Tests\Client\Handler;

use Spike\Client\Handler\RegisterTunnelResponseHandler;
use Spike\Protocol\Spike;
use Spike\Tests\TestCase;
use Spike\Client\EventStore;
use Slince\Event\Event;

class RegisterTunnelResponseTest extends TestCase
{
    public function testHandle()
    {
        $client = $this->getClientStub();
        $handler = new RegisterTunnelResponseHandler($client, $this->getConnectionMock());
        $this->assertNull($client->getClientId());
        $message = new Spike('register_tunnel_response', ['serverPort' => 8086, 'error' => 'foo'], [
            'Code' => 1
        ]);
        $client->getDispatcher()->addListener(EventStore::REGISTER_TUNNEL_ERROR, function(Event $event) use ($message){
            $this->assertEquals('8086', $event->getArgument('tunnel')->getServerPort());
            $this->assertEquals('foo', $event->getArgument('errorMessage'));
        });

        $handler->handle($message);
        $this->assertNull($client->getClientId());

        $message = new Spike('register_tunnel_response', ['serverPort' => 8086], [
            'Code' => 0
        ]);
        $client->getDispatcher()->addListener(EventStore::REGISTER_TUNNEL_SUCCESS, function(Event $event) use ($message){
            $this->assertEquals('8086', $event->getArgument('tunnel')->getServerPort());
        });
        $handler->handle($message);
    }
}