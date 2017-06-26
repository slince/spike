<?php
namespace Spike\Tests\Client\Handler;

use Slince\Event\Event;
use Spike\Client\EventStore;
use Spike\Client\Handler\AuthResponseHandler;
use Spike\Protocol\Spike;
use Spike\Tests\TestCase;

class AuthResponseHandlerTest extends TestCase
{
    public function testHandle()
    {
        $client = $this->getClientStub();
        $handler = new AuthResponseHandler($client, $this->getConnectionMock());
        $this->assertNull($client->getClientId());
        $message = new Spike('auth_response', ['id' => 'foo-id'], [
            'Code' => 1
        ]);
        $client->getDispatcher()->addListener(EventStore::AUTH_ERROR, function(Event $event) use ($message){
            $this->assertEquals($message, $event->getArgument('message'));
        });
        $handler->handle($message);
        $this->assertNull($client->getClientId());

        $message = new Spike('auth_response',  ['id' => 'foo-id'], [
            'Code' => 0
        ]);
        $client->getDispatcher()->addListener(EventStore::AUTH_SUCCESS, function(Event $event) use ($message){
            $this->assertEquals($message, $event->getArgument('message'));
        });
        $handler->handle($message);
        $this->assertEquals('foo-id', $client->getClientId());
    }
}