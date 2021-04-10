<?php
namespace Spike\Tests\Server\Handler;

use Spike\Common\Protocol\Spike;
use Spike\Server\Client;
use Spike\Server\Handler\RegisterTunnelAwareHandler;
use Spike\Tests\TestCase;

class RegisterTunnelHandlerTest extends TestCase
{
    protected $server;

    public function testHandle()
    {
        $client = new Client([
            'os' => PHP_OS,
            'version' => '',
        ], $this->getConnectionMock());
        $server = $this->getServerMock();
        $server->getClients()->add($client);

        $handler = new RegisterTunnelAwareHandler($server, $this->getConnectionMock());
        $message = new Spike('register_tunnel', [
            'protocol' => 'tcp',
            'host' => '127.0.0.1',
            'serverPort' => 8086
        ], [
            'client-id' => $client->getId()
        ]);
        $handler->handle($message);
        $this->assertCount(1, $server->getChunkServers());
        $message2 = new Spike('register_tunnel', [
            'protocol' => 'tcp',
            'host' => '127.0.0.1',
            'serverPort' => 8087
        ], [
            'client-id' => $client->getId()
        ]);
        $handler->handle($message2);
        $this->assertCount(2, $server->getChunkServers());
        $handler->handle($message2);
        $this->assertCount(2, $server->getChunkServers());
        $this->server = $server;
    }
}