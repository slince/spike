<?php

namespace Spike\Server\Handler;

use Spike\Exception\InvalidArgumentException;

trait AuthenticationAwareTrait
{
    public function ensureClientValid(string $clientId)
    {
        $client = $this->clients->get($clientId);
        if (null === $client) {
            throw new InvalidArgumentException(sprintf('The client "%s" is not found.', $clientId));
        } elseif (!$client->isAuthenticated()) {
            throw new InvalidArgumentException(sprintf('The client "%s" is not authenticated.', $clientId));
        }
    }
}