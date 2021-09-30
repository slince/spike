<?php

namespace Spike\Socket;

use React\Socket\ConnectionInterface;

interface ServerInterface
{
    /**
     * Add an event listener.
     *
     * @param string $event
     * @param callable $listener
     */
    public function on(string $event, callable $listener);

    /**
     * Configure the server.
     *
     * @param array $options
     */
    public function configure(array $options);

    /**
     * Start the server.
     */
    public function serve();

    /**
     * {@internal}
     */
    public function handleConnection(ConnectionInterface $connection);
}