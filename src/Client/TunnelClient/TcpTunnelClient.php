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
        $localConnection->pipe($this->proxyConnection);
        $this->proxyConnection->pipe($localConnection);
        $localConnection->write($this->initBuffer);

        $localConnection->on('end', function(){
            var_dump('local end');
        });

        //Handles the local connection close
        $handleLocalConnectionClose = function() use (&$handleProxyConnectionClose){
            var_dump('local close');
            $this->proxyConnection->removeListener('close', $handleProxyConnectionClose);
            $this->proxyConnection->removeListener('error', $handleProxyConnectionClose);
            $this->proxyConnection->end();
            $this->client->getTunnelClients()->removeElement($this);
        };
        $localConnection->on('close', $handleLocalConnectionClose);
        $localConnection->on('error', $handleLocalConnectionClose);

        //Handles the proxy connection close
        $handleProxyConnectionClose = function() use ($localConnection, &$handleLocalConnectionClose){
            var_dump('tunnel close');
            $localConnection->removeListener('close', $handleLocalConnectionClose);
            $localConnection->removeListener('error', $handleLocalConnectionClose);
            $localConnection->end();
            $this->client->getTunnelClients()->removeElement($this);
        };
        $this->proxyConnection->on('close', $handleProxyConnectionClose);
        $this->proxyConnection->on('error', $handleProxyConnectionClose);
    }
}