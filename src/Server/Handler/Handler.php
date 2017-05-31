<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use React\Socket\ConnectionInterface;
use Spike\Server\Server;

abstract class Handler implements HandlerInterface
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function __construct(Server $server, ConnectionInterface $connection)
    {
        $this->server = $server;
        $this->connection = $connection;
    }
}