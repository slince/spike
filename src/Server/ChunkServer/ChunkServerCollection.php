<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Server;

use Doctrine\Common\Collections\ArrayCollection;
use React\Socket\ConnectionInterface;
use Spike\Server\ChunkServer\ChunkServerInterface;

class ChunkServerCollection extends ArrayCollection
{
    /**
     * Finds tunnel servers by the given control connection
     * @param ConnectionInterface $connection
     * @return ChunkServerInterface[]
     */
    public function filterByControlConnection(ConnectionInterface $connection)
    {
        return parent::filter(function(ChunkServerInterface $tunnelServer) use ($connection){
            return $tunnelServer->getControlConnection() === $connection;
        })->toArray();
    }

    /**
     * Finds the tunnel server by the tunnel information
     * @param array $tunnelInfo
     * @return ChunkServerInterface
     */
    public function findByTunnelInfo($tunnelInfo)
    {
        return parent::filter(function(ChunkServerInterface $tunnelServer) use ($tunnelInfo){
            return $tunnelServer->getTunnel()->match($tunnelInfo);
        })->first();
    }
}