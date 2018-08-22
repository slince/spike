<?php
namespace Spike\Tests\Server;

use React\EventLoop\LoopInterface;
use Slince\EventDispatcher\Dispatcher;
use Spike\Common\Logger\Logger;
use Spike\Common\Tunnel\HttpTunnel;
use Spike\Common\Tunnel\TcpTunnel;
use Spike\Server\Client;
use Spike\Tests\TestCase;

class ServerTest extends TestCase
{
    public function testGetter()
    {
        $server = $this->getServerMock();
        $this->assertInstanceOf(Dispatcher::class, $server->getEventDispatcher());
        $this->assertCount(0, $server->getClients());
        $this->assertCount(0, $server->getChunkServers());
        $this->assertInstanceOf(LoopInterface::class, $server->getEventLoop());
        $this->assertInstanceOf(Logger::class, $server->getLogger());
    }


    public function testClient()
    {
        $server = $this->getServerMock();
        $connection = $this->getConnectionMock();
        $client = new Client([], $connection);
        $server->getClients()->add($client);
        $this->assertCount(1, $server->getClients());
    }
}