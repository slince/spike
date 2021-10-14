<?php

namespace Spike\Server\Handler;

use React\Socket\ConnectionInterface;
use Spike\Protocol\Message;
use Spike\Server\Client;

abstract class AuthAwareHandlerServer extends ServerCommandHandler
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message, ConnectionInterface $connection)
    {
        $clientId = $message->getArgument('client-id');
        $client = $this->server->getClientById($clientId);
        if (null === $client) {
            $connection->end(new ErrorMessage('Cannot find the client'));
        } else {
            $this->client = $client;
            $this->client->setActiveAt(new \DateTime()); //Update last active time.
        }
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}