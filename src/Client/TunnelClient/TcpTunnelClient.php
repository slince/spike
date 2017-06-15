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
        $this->tunnel->pipe($localConnection);
        $this->tunnel->getConnection()->on('data', function($data){
            echo $data;
        });
    }
}