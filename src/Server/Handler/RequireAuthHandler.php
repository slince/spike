<?php

namespace Spike\Server\Handler;

use Slince\EventDispatcher\Event;
use Spike\Common\Protocol\SpikeInterface;
use Spike\Server\Client;
use Spike\Server\Event\Events;

class RequireAuthHandler extends MessageMessageHandler
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        $clientId = $message->getHeader('client-id');
        $client = $this->server->getClientById($clientId);
        if (!$client){
            $event = new Event(Events::UNAUTHORIZED_CLIENT, $this, [
                'clientId' => $clientId,
                'connection' => $this->connection,
            ]);
            $this->getEventDispatcher()->dispatch($event);
            $this->connection->close();
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