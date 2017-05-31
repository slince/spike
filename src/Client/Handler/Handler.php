<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use React\Socket\ConnectionInterface;
use Spike\Client\Client;
use Spike\Server\Server;

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
}