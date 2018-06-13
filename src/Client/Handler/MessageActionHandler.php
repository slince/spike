<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Client\Handler;

use React\Socket\ConnectionInterface;
use Slince\Event\DispatcherInterface;
use Spike\Client\Client;

abstract class MessageActionHandler implements ActionHandlerInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function __construct(Client $client, ConnectionInterface $connection)
    {
        $this->client = $client;
        $this->connection = $connection;
    }

    /**
     * Gets the event dispatcher
     * @return  DispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->client->getEventDispatcher();
    }

    /**
     * Write data to connection
     * @param string $data
     */
    public function write($data)
    {
        $this->connection->write($data);
        $this->client->setActiveAt(new \DateTime('now'));
    }
}