<?php
namespace Spike\Tests\Client\Handler;

use Slince\Event\Event;
use Spike\Client\Event\Events;
use Spike\Client\Handler\AuthResponseHandler;
use Spike\Common\Protocol\Spike;
use Spike\Tests\TestCase;

class AuthResponseHandlerTest extends TestCase
{
    public function testHandle()
    {
        $client = $this->getClientStub();
        $client->setLogger($this->getLoggerStub());
        $handler = new AuthResponseHandler($client, $this->getConnectionMock());
        $this->assertNull($client->getId());
        $message = new Spike('auth_response', ['id' => 'foo-id'], [
            'code' => 1
        ]);
        $client->getEventDispatcher()->addListener(Events::AUTH_ERROR, function(Event $event) use ($message){
            $this->assertEquals($message, $event->getArgument('message'));
        });
        $handler->handle($message);
        $this->assertNull($client->getId());

        $message = new Spike('auth_response',  ['id' => 'foo-id'], [
            'code' => 200
        ]);
        $client->getEventDispatcher()->addListener(Events::AUTH_SUCCESS, function(Event $event) use ($message){
            $this->assertEquals($message, $event->getArgument('message'));
        });
        $handler->handle($message);
        $this->assertEquals('foo-id', $client->getId());
    }
}