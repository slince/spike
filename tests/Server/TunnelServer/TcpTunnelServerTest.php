<?php
namespace Spike\Tests\Server\TunnelServer;

use React\Socket\ConnectionInterface;
use Spike\Server\TunnelServer\TcpTunnelServer;
use Spike\Tests\TestCase;
use Spike\Tunnel\TcpTunnel;

class TcpTunnelServerTest extends TestCase
{
    public function testConstruct()
    {
        $server = $this->getServerMock();
        $connection = $this->getConnectionMock();
        $tunnel = new TcpTunnel(8089, '127.0.0.1:80');
        $tunnelServer = new TcpTunnelServer($server, $connection, $tunnel, $this->getLoop());
        $this->assertInstanceOf(ConnectionInterface::class, $tunnelServer->getControlConnection());
        $this->assertEquals($tunnel, $tunnelServer->getTunnel());
        $this->assertEquals($this->getLoop(), $tunnelServer->getLoop());
        $this->assertEquals($server->getDispatcher(), $tunnelServer->getDispatcher());
    }
}