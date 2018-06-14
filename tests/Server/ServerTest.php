<?php
namespace Spike\Tests\Server;

use React\EventLoop\LoopInterface;
use Slince\Event\Dispatcher;
use Spike\Logger\Logger;
use Spike\Server\Client;
use Spike\Tests\TestCase;
use Spike\Tunnel\HttpTunnel;
use Spike\Tunnel\TcpTunnel;

class ServerTest extends TestCase
{
    public function testGetter()
    {
        $server = $this->getServerMock();
        $this->assertInstanceOf(Dispatcher::class, $server->getDispatcher());
        $this->assertCount(0, $server->getClients());
        $this->assertCount(0, $server->getTunnelServers());
        $this->assertEquals('127.0.0.1', $server->getHost());
        $this->assertEquals('8088', $server->getPort());
        $this->assertInstanceOf(LoopInterface::class, $server->getLoop());
        $this->assertNull($server->getLogger());
        $server->setLogger($this->createMock(Logger::class));
        $this->assertInstanceOf(Logger::class, $server->getLogger());
    }

    public function testTunnelServer()
    {
        $server = $this->getServerMock();
        $this->assertCount(0, $server->getTunnelServers());
        $tunnel = new HttpTunnel(8086, [
            'www.foo.com' => '127.0.0.1:8090'
        ]);
        $server->createTunnelServer($tunnel, $this->getConnectionMock());
        $this->assertCount(1, $server->getTunnelServers());
        $tunnel = new TcpTunnel(8087,'127.0.0.1:8090');
        $server->createTunnelServer($tunnel, $this->getConnectionMock());
        $this->assertCount(2, $server->getTunnelServers());
    }

    public function testClient()
    {
        $server = $this->getServerMock();
        $connection = $this->getConnectionMock();
        $client = new Client([], $connection);
        $server->getClients()->add($client);
        $this->assertCount(1, $server->getClients());
        $tunnel = new HttpTunnel(8086, [
            'www.foo.com' => '127.0.0.1:8090'
        ]);
        $server->createTunnelServer($tunnel, $connection);
        $this->assertCount(1, $server->getTunnelServers());
        $server->closeClient($client);
        $this->assertCount(0, $server->getClients());
        $this->assertCount(0, $server->getTunnelServers());
    }
}