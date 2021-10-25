<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Socket;

use React\EventLoop\LoopInterface;
use React\Socket\ServerInterface as Socket;

class Worker
{
    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * @var Socket
     */
    protected $socket;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var callable[]
     */
    protected $signals = [];

    public function __construct(LoopInterface $loop, ServerInterface $server)
    {
        $this->loop = $loop;
        $this->server = $server;
        $this->socket = $server->getSocket();
    }

    /**
     * Starts the worker.
     */
    public function start()
    {
    }

    /**
     * Close the worker.
     *
     * {@internal}
     */
    public function close()
    {
    }

    public function restart()
    {
    }

    /**
     * Register signal handler.
     * @param $signal
     * @param callable $handler
     */
    public function onSignal($signal, callable $handler)
    {
        $this->signals[$signal] = $handler;
    }

    /**
     * @internal
     */
     public function work()
     {
         $this->socket->on('connection', [$this->server, 'handleConnection']);
         $this->socket->on('error', [$this->server, 'handleError']);
     }
}