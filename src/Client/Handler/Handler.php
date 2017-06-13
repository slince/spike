<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use React\Socket\ConnectionInterface;
use Spike\Client\Client;
use Slince\Event\Dispatcher;

abstract class Handler implements HandlerInterface
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
     * @return  Dispatcher
     */
    public function getDispatcher()
    {
        return $this->client->getDispatcher();
    }
}