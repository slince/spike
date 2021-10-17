<?php

namespace Spike\Server\Handler;

use Spike\Connection\ConnectionInterface;
use Spike\Exception\InvalidArgumentException;

trait AuthenticationAwareTrait
{
    public function ensureClientIdValid(string $clientId)
    {
        $client = $this->clients->get($clientId);
        if (null === $client) {
            throw new InvalidArgumentException(sprintf('The client "%s" is not found.', $clientId));
        } elseif (!$client->isAuthenticated()) {
            throw new InvalidArgumentException(sprintf('The client "%s" is not authenticated.', $clientId));
        }
    }

    public function ensureClientConnectionValid(ConnectionInterface $connection)
    {
        $client = $this->clients->search($connection);
        if (null === $client) {
            throw new InvalidArgumentException('The client with given connection is not found.');
        } elseif (!$client->isAuthenticated()) {
            throw new InvalidArgumentException('The client with given connection is not authenticated.');
        }
    }
}