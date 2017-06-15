<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\Socket\ConnectionInterface;
use Spike\Protocol\Spike;
use Spike\Protocol\StartProxy;

class TcpTunnelServer extends TunnelServer
{
    public function handleProxyConnection(ConnectionInterface $proxyConnection)
    {
        $this->tunnel->getControlConnection()->write(new Spike('request_proxy', $this->tunnel->toArray()));
        $this->tunnel->pipe($proxyConnection);
        $this->pause();
    }
}