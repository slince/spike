<?php
namespace Spike\Tests\Client\Handler;

use Spike\Client\Event\Events;
use Spike\Client\Handler\RequestProxyHandler;
use Spike\Common\Exception\InvalidArgumentException;
use Spike\Common\Protocol\Spike;
use Spike\Tests\TestCase;
use Slince\Event\Event;

class RequestProxyHandlerTest extends TestCase
{
    public function testHandle()
    {
        $client = $this->getClientStub();
        $client->setLogger($this->getLoggerStub());
        $handler = new RequestProxyHandler($client, $this->getConnectionMock());
        $message = new Spike('request_proxy', ['serverPort' => 8086, 'error' => 'foo'], [
            'Proxy-Connection-ID' => 1
        ]);
        $client->getEventDispatcher()->addListener(Events::REQUEST_PROXY, function(Event $event) use ($message){
            $this->assertEquals('8086', $event->getArgument('tunnel')->getServerPort());
            $this->assertNotNull($event->getArgument('client'));
        });
        $handler->handle($message);
        $this->assertNull($client->getId());
        $this->expectException(InvalidArgumentException::class);
        $handler->handle(new Spike('request_proxy', ['serverPort' => 808888, 'error' => 'foo'], [
            'public-connection-id' => 1
        ]));
    }
}