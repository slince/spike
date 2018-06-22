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

use React\Socket\ConnectionInterface;
use Slince\EventDispatcher\Dispatcher;
use Spike\Server\Server;

abstract class MessageActionHandler implements ActionHandlerInterface
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
     * Gets the event dispatcher.
     *
     * @return Dispatcher
     */
    public function getEventDispatcher()
    {
        return $this->server->getEventDispatcher();
    }
}