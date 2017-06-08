<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\Socket\ConnectionInterface;
use Spike\Protocol\StartProxy;

class TcpTunnelServer extends TunnelServer
{
    public function handleConnection(ConnectionInterface $connection)
    {
        $message = new StartProxy([
            'port' => $this->tunnel->getPort(),
            'clientIp' => $connection->getLocalAddress()
        ]);
        $this->tunnel->getConnection()->write($message);
        $this->tunnel->pipe($connection);
    }
}