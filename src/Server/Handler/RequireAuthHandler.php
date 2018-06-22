<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Handler;

use Slince\EventDispatcher\Event;
use Spike\Common\Protocol\SpikeInterface;
use Spike\Server\Client;
use Spike\Server\Event\Events;

class RequireAuthHandler extends MessageActionHandler
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
        }
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sends data to client.
     *
     * @param string $buffer
     */
    protected function sendToClient($buffer)
    {
        $this->connection->write($buffer);
        $this->client->setActiveAt(new \DateTime());
    }
}