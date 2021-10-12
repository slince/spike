<?php


namespace Spike\Server;

use Spike\Connection\ConnectionInterface;

final class ClientRegistry
{
    /**
     * @var Client[]
     */
    protected $clients = [];

    protected $storage;

    public function __construct()
    {
        $this->storage = new \SplObjectStorage();
    }

    public function add(Client $client)
    {
        $this->clients[$client->getId()] = $client;
        $this->storage->attach($client->getConnection(), $client);
    }

    public function get(string $id): ?Client
    {
        return $this->clients[$id] ?? null;
    }

    public function search(ConnectionInterface $connection)
    {
        return $this->storage->contains($connection)
            ? $this->storage->offsetGet($connection) : null;
    }
}