<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\TunnelClient;

use React\Socket\ConnectionInterface;

class TcpTunnelClient extends TunnelClient
{
    public function handleLocalConnection(ConnectionInterface $localConnection)
    {
        $localConnection->pipe($this->tunnelConnection);
        $this->tunnelConnection->pipe($localConnection);
        $localConnection->write($this->initBuffer);
    }
}