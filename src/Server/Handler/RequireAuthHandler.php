<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Slince\Event\Event;
use Spike\Exception\ForbiddenException;
use Spike\Server\Client;
use Spike\Protocol\SpikeInterface;
use Spike\Server\EventStore;

class RequireAuthHandler extends MessageHandler
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
        $clientId = $message->getHeader('Client-ID');
        if (!$clientId || !($client = $this->server->getClients()->findById($clientId))) {
            $this->getDispatcher()->dispatch(new Event(EventStore::UNAUTHORIZED_CLIENT, $this, [
                'clientId' => $clientId,
                'connection' => $this->connection
            ]));
            $this->connection->close();
            throw new ForbiddenException();
        }
        $this->client = $client;
    }
}