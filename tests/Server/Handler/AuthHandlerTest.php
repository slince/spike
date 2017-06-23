<?php
namespace Spike\Tests\Server\Handler;

use Spike\Protocol\Spike;
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
        $this->assertEquals($server, $handler->getServer());
        $this->assertCount(1, $server->getClients());

        $message = new Spike('auth', [
            'os' => PHP_OS,
            'version' => '',
            'username' => 'foo',
            'password' => 'baz'
        ]);
        $handler->handle($message);
        $this->assertCount(1, $server->getClients());
    }
}