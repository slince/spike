<?php


namespace Spike\Server;

use Spike\Connection\ConnectionInterface;

final class ClientRegistry
{
    /**
     * @var Client[]
     */
    protected $clients = [];

    protected $connectionMap;

    public function __construct()
    {
        $this->connectionMap = hash_ob
    }

    public function add(Client $client)
    {
        $this->clients[$client->getId()] =  $client;
    }

    public function get(string $id): ?Client
    {
        return $this->clients[$id] ?? null;
    }

    public function search(ConnectionInterface $connection)
    {

    }
}