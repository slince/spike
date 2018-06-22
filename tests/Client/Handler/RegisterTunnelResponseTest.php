<?php
namespace Spike\Tests\Client\Handler;

use Spike\Client\Event\Events;
use Spike\Client\Handler\RegisterTunnelResponseHandler;
use Spike\Common\Protocol\Spike;
use Spike\Tests\TestCase;
use Slince\EventDispatcher\Event;

class RegisterTunnelResponseTest extends TestCase
{
    public function testHandle()
    {
        $client = $this->getClientStub();
        $client->setLogger($this->getLoggerStub());
        $handler = new RegisterTunnelResponseHandler($client, $this->getConnectionMock());
        $this->assertNull($client->getId());
        $message = new Spike('register_tunnel_response', ['serverPort' => 8086, 'error' => 'foo'], [
            'code' => 1
        ]);
        $client->getEventDispatcher()->addListener(Events::REGISTER_TUNNEL_ERROR, function(Event $event) use ($message){
            $this->assertEquals('8086', $event->getArgument('tunnel')->getServerPort());
            $this->assertEquals('foo', $event->getArgument('errorMessage'));
        });

        $handler->handle($message);
        $this->assertNull($client->getId());

        $message = new Spike('register_tunnel_response', ['serverPort' => 8086], [
            'code' => 200
        ]);
        $client->getEventDispatcher()->addListener(Events::REGISTER_TUNNEL_SUCCESS, function(Event $event) use ($message){
            $this->assertEquals('8086', $event->getArgument('tunnel')->getServerPort());
        });
        $handler->handle($message);
    }
}