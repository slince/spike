<?php
namespace Spike\Tests\Server\Handler;

use Spike\Exception\ForbiddenException;
use Spike\Server\Client;
use Spike\Server\Handler\RequireAuthHandler;
use Spike\Protocol\Spike;
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

        $handler = new RequireAuthHandler($server, $this->getConnectionMock());
        $message = new Spike('ping', null, [
            'Client-ID' => $client->getId()
        ]);
        $handler->handle($message);
    }

    public function testForbidden()
    {
        $server = $this->getServerMock();
        $message = new Spike('ping', null, [
            'Client-ID' => 'foo'
        ]);
        $handler = new RequireAuthHandler($server, $this->getConnectionMock());
        try {
            $handler->handle($message);
            $this->fail();
        } catch (\Exception $exception) {
            $this->assertInstanceOf(ForbiddenException::class, $exception);
        }

        $message = new Spike('ping', null);
        $handler = new RequireAuthHandler($server, $this->getConnectionMock());
        try {
            $handler->handle($message);
            $this->fail();
        } catch (\Exception $exception) {
            $this->assertInstanceOf(ForbiddenException::class, $exception);
        }
    }
}