<?php

namespace Spike\Connection;

use Spike\Command\CommandInterface;

interface ConnectionInterface
{
    /**
     * Closes the connection to Spiked.
     *
     * @param bool $force
     */
    public function disconnect(bool $force = false);

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
     */
    public function executeCommand(CommandInterface $command);

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

    /**
     *  Pipes all the data from this readable source into the given writable destination.
     *
     * @param ConnectionInterface $dest
     */
    public function pipe(ConnectionInterface $dest);
}