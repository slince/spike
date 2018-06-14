<?php
namespace Spike\Tests\Server\ChunkServer;

use React\Socket\ConnectionInterface;
use Spike\Server\ChunkServer\TcpChunkServer;
use Spike\Tests\TestCase;
use Spike\Tunnel\TcpTunnel;

class TcpChunkServerTest extends TestCase
{
    public function testConstruct()
    {
        $server = $this->getServerMock();
        $connection = $this->getConnectionMock();
        $tunnel = new TcpTunnel(8089, '127.0.0.1:80');
        $ChunkServer = new TcpChunkServer($server, $connection, $tunnel, $this->getEventLoop());
        $this->assertInstanceOf(ConnectionInterface::class, $ChunkServer->getControlConnection());
        $this->assertEquals($tunnel, $ChunkServer->getTunnel());
        $this->assertEquals($this->getEventLoop(), $ChunkServer->getLoop());
        $this->assertEquals($server->getDispatcher(), $ChunkServer->getDispatcher());

        $this->assertEquals($connection, $ChunkServer->getControlConnection());
        $this->assertNull($ChunkServer->getSocket());
    }
}