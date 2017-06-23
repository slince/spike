<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use Doctrine\Common\Collections\ArrayCollection;
use React\Socket\ConnectionInterface;
use Spike\Server\TunnelServer\TunnelServer;
use Spike\Server\TunnelServer\TunnelServerInterface;

class TunnelServerCollection extends ArrayCollection
{
    /**
     * Finds tunnel servers by the given control connection
     * @param ConnectionInterface $connection
     * @return TunnelServerInterface[]
     */
    public function filterByControlConnection(ConnectionInterface $connection)
    {
        return parent::filter(function(TunnelServerInterface $tunnelServer) use ($connection){
            return $tunnelServer->getControlConnection() === $connection;
        })->toArray();
    }

    /**
     * Finds the tunnel server by the tunnel information
     * @param array $tunnelInfo
     * @return TunnelServer
     */
    public function findByTunnelInfo($tunnelInfo)
    {
        return parent::filter(function(TunnelServerInterface $tunnelServer) use ($tunnelInfo){
            return $tunnelServer->getTunnel()->match($tunnelInfo);
        })->first();
    }
}