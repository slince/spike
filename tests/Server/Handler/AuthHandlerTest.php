<?php
namespace Spike\Tests\Server\Handler;

use Spike\Common\Protocol\Spike;
use Spike\Server\Handler\AuthHandler;
use Spike\Tests\TestCase;

class AuthHandlerTest extends TestCase
{
    public function testHandle()
    {
        $server = $this->getServerMock();
        $handler = new AuthHandler($server, $this->getConnectionMock());
        $message = new Spike('auth', [
            'os' => PHP_OS,
            'version' => '',
            'username' => 'foo',
            'password' => 'bar'
        ]);
        $handler->handle($message);
        $this->assertCount(1, $server->getClients());
    }

    public function testHandleWrongPassword()
    {
        $message = new Spike('auth', [
            'os' => PHP_OS,
            'version' => '',
            'username' => 'foo',
            'password' => 'baz'
        ]);
        $server = $this->getServerMock();
        $handler = new AuthHandler($server, $this->getConnectionMock());
        $handler->handle($message);
        $this->assertCount(0, $server->getClients());

    }
    public function testHandleMissingUsername()
    {
        $message = new Spike('auth', [
            'os' => PHP_OS,
            'version' => '',
            'password' => 'baz'
        ]);
        $server = $this->getServerMock();
        $handler = new AuthHandler($server, $this->getConnectionMock());
        $handler->handle($message);
        $this->assertCount(0, $server->getClients());
    }
}