<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use Doctrine\Common\Collections\ArrayCollection;
use React\Socket\ConnectionInterface;
use Spike\Server\TunnelServer\TunnelServerInterface;

class TunnelServerCollection extends ArrayCollection
{
    /**
     * @param ConnectionInterface $connection
     * @return TunnelServerInterface[]
     */
    public function filterByControlConnection(ConnectionInterface $connection)
    {
        return parent::filter(function(TunnelServerInterface $tunnelServer) use ($connection){
            return $tunnelServer->getControlConnection() === $connection;
        })->toArray();
    }

    public function findByTunnelInfo($tunnelInfo)
    {
        return parent::filter(function(TunnelServerInterface $tunnelServer) use ($tunnelInfo){
            return $tunnelServer->getTunnel()->match($tunnelInfo);
        })->first();
    }
}