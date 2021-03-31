<?php

namespace Spike;

use React\Socket\ConnectionInterface;

interface ServerInterface
{
    /**
     * Add an listener.
     *
     * @param string $event
     * @param callable $listener
     */
    public function on($event, callable $listener);

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
     * @param ConnectionInterface $connection
     * {@internal }
     */
    public function handleConnection(ConnectionInterface $connection);
}