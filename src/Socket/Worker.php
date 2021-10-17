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

use Spike\Process\FakeProcess;
use Spike\Process\Process;
use Spike\Process\ProcessInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ServerInterface as Socket;

final class Worker
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
     * @var ProcessInterface
     */
    protected $process;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var callable[]
     */
    protected $signals = [];

    public function __construct(LoopInterface $loop, ServerInterface $server, Socket $socket)
    {
        $this->loop = $loop;
        $this->server = $server;
        $this->socket = $socket;
    }

    /**
     * Starts the worker.
     */
    public function start()
    {
        $this->process = Worker::createProcess([$this, 'work']);
        $this->initialize();
        $this->process->start(false);
    }

    /**
     * Stop the worker.
     */
    public function stop()
    {
        $this->process->stop();
    }

    public function restart()
    {
        $this->process->stop();
        $this->start();;
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
     * Gets the worker pid.
     *
     * @return int
     */
    public function getPid(): int
    {
        return $this->process->getPid();
    }

    /**
     * Close the worker.
     *
     * {@internal }
     */
    public function close()
    {
        $this->loop->stop();
    }

    protected function initialize()
    {
        if (function_exists('pcntl_signal')) {
            $this->onSignal(SIGTERM, [$this, 'close']);
            $this->onSignal(SIGUSR1, [$this, 'retry']);
        }
    }

    protected static function createProcess(callable $callback)
    {
        if (function_exists('pcntl_fork')) {
            return new Process($callback);
        }
        return new FakeProcess($callback);
    }

    /**
     * @internal
     */
     public function work()
     {
         foreach ($this->signals as $signal => $handler) {
             $this->loop->addSignal($signal, $handler);
         }
        $this->socket->on('connection', [$this->server, 'handleConnection']);
        $this->loop->run();
     }
}