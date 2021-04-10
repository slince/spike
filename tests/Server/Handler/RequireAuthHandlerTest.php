<?php
namespace Spike\Tests\Server\Handler;

use Spike\Common\Exception\ForbiddenException;
use Spike\Common\Protocol\Spike;
use Spike\Server\Client;
use Spike\Server\Handler\AuthAwareHandler;
use Spike\Tests\TestCase;

class RequireAuthHandlerTest extends TestCase
{
    public function testHandle()
    {
        $client = new Client([
            'os' => PHP_OS,
            'version' => '',
        ], $this->getConnectionMock());
        $server = $this->getServerMock();
        $server->getClients()->add($client);
        $handler = new AuthAwareHandler($server, $this->getConnectionMock());
        $message = new Spike('ping', null, [
            'client-id' => $client->getId()
        ]);
        $this->assertNull($handler->getClient());
        $handler->handle($message);
        $this->assertEquals($client, $handler->getClient());
    }

    public function testForbidden()
    {
        $server = $this->getServerMock();
        $message = new Spike('ping', null, [
            'client-id' => 'foo'
        ]);
        $handler = new AuthAwareHandler($server, $this->getConnectionMock());
        $handler->handle($message);
        $this->assertNull($handler->getClient());
    }
}