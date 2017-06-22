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
        $this->localConnection = $localConnection;
        $localConnection->pipe($this->proxyConnection);
        $this->proxyConnection->pipe($localConnection);
        $localConnection->write($this->initBuffer);

        //Handles the local connection close
        $handleLocalConnectionClose = function(){
            $this->close();
        };
        $localConnection->on('close', $handleLocalConnectionClose);
        $localConnection->on('error', $handleLocalConnectionClose);

        //Handles the proxy connection close
        $handleProxyConnectionClose = function(){
            $this->close();
        };
        $this->proxyConnection->on('close', $handleProxyConnectionClose);
        $this->proxyConnection->on('error', $handleProxyConnectionClose);
    }
}