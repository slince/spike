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

        $localConnection->on('end', function(){
            var_dump('local end');
        });

        //Handles the local connection close
        $handleLocalConnectionClose = function() use (&$handleTunnelConnectionClose){
            var_dump('local close');
            $this->tunnelConnection->removeListener('close', $handleTunnelConnectionClose);
            $this->tunnelConnection->removeListener('error', $handleTunnelConnectionClose);
            $this->tunnelConnection->end();
            $this->client->getTunnelClients()->removeElement($this);
        };
        $localConnection->on('close', $handleLocalConnectionClose);
        $localConnection->on('error', $handleLocalConnectionClose);

        //Handles the tunnel connection close
        $handleTunnelConnectionClose = function() use ($localConnection, &$handleLocalConnectionClose){
            var_dump('tunnel close');
            $localConnection->removeListener('close', $handleLocalConnectionClose);
            $localConnection->removeListener('error', $handleLocalConnectionClose);
            $localConnection->end();
            $this->client->getTunnelClients()->removeElement($this);
        };
        $this->tunnelConnection->on('close', $handleTunnelConnectionClose);
        $this->tunnelConnection->on('error', $handleTunnelConnectionClose);
    }
}