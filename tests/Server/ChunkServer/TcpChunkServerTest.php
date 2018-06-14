<?php
namespace Spike\Tests\Server\ChunkServer;

use React\Socket\ConnectionInterface;
use Spike\Common\Tunnel\TcpTunnel;
use Spike\Server\ChunkServer\TcpChunkServer;
use Spike\Server\Client;
use Spike\Tests\TestCase;

class TcpChunkServerTest extends TestCase
{
    public function testConstruct()
    {
        $server = $this->getServerMock();
        $connection = $this->getConnectionMock();
        $tunnel = new TcpTunnel(8089, '127.0.0.1:80');

        $client = new Client([], $connection);
        $chunkServer = new TcpChunkServer($server, $client, $tunnel);

        $this->assertEquals($tunnel, $chunkServer->getTunnel());
        $this->assertEquals($this->getEventLoop(), $chunkServer->getEventLoop());
    }
}