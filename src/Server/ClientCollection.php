<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use Doctrine\Common\Collections\ArrayCollection;
use React\Socket\ConnectionInterface;

class ClientCollection extends ArrayCollection
{
    /**
     * Finds the client by given connection
     * @param ConnectionInterface $connection
     * @return Client
     */
    public function findByConnection(ConnectionInterface $connection)
    {
        return parent::filter(function(Client $client) use ($connection){
            return $client->getControlConnection() === $connection;
        })->first();
    }

    /**
     * Finds the client by it
     * @param string $id
     * @return Client
     */
    public function findById($id)
    {
        return parent::filter(function(Client $client) use ($id){
            return $client->getId() === $id;
        })->first();
    }
}