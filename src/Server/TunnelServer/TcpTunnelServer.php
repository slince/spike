<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;

class TcpTunnelServer extends TunnelServer
{
    public function handleConnection(ConnectionInterface $connection)
    {
        $this->tunnel->pipe($connection);
    }
}