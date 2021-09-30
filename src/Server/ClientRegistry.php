<?php


namespace Spike\Server;

final class ClientRegistry
{
    /**
     * @var Client[]
     */
    protected $clients;

    public function add(Client $client)
    {
        $this->clients[$client->getId()] =  $client;
    }

    public function get(string $id): ?Client
    {
        return $this->clients[$id] ?? null;
    }
}