<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use React\Socket\ConnectionInterface;
use Slince\Event\Dispatcher;
use Spike\Server\Server;

abstract class MessageHandler implements HandlerInterface
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

    /**
     * Gets the event dispatcher
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->server->getDispatcher();
    }

    /**
     * Gets the server instance
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }
}