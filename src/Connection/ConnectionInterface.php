<?php

namespace Spike\Connection;

use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Spike\Command\CommandInterface;

interface ConnectionInterface
{
    /**
     * Closes the connection to Spiked.
     */
    public function disconnect();

    /**
     * Writes the request for the given command over the connection.
     *
     * @param CommandInterface $command Command instance.
     */
    public function writeRequest(CommandInterface $command);

    /**
     * Writes a request for the given command over the connection.
     *
     * @param CommandInterface $command Command instance.
     *
     * @return PromiseInterface
     */
    public function executeCommand(CommandInterface $command): PromiseInterface;

    /**
     * Register the message handler to handle raw message.
     *
     * @param callable $callback
     */
    public function listenRaw(callable $callback);

    /**
     * Register the message handler.
     *
     * @param callable $callback
     */
    public function listen(callable $callback);
}