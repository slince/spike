<?php
namespace Spike\Tests\Server\Handler;

use Spike\Server\Client;
use Spike\Protocol\Spike;
use Spike\Server\Handler\RegisterTunnelHandler;
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

        $handler = new RegisterTunnelHandler($server, $this->getConnectionMock());
        $message = new Spike('register_tunnel', [
            'protocol' => 'tcp',
            'host' => '127.0.0.1',
            'serverPort' => 8086
        ], [
            'Client-ID' => $client->getId()
        ]);
        $handler->handle($message);
        $this->assertCount(1, $server->getChunkServers());
        $message2 = new Spike('register_tunnel', [
            'protocol' => 'tcp',
            'host' => '127.0.0.1',
            'serverPort' => 8087
        ], [
            'Client-ID' => $client->getId()
        ]);
        $handler->handle($message2);
        $this->assertCount(2, $server->getChunkServers());
        $handler->handle($message2);
        $this->assertCount(2, $server->getChunkServers());
        $this->server = $server;
    }

    public function tearDown()
    {
        foreach ($this->server->getClients() as $client) {
            $this->server->closeClient($client);
        }
    }
}