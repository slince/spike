<?php

namespace Spike\Connection;

use React\Stream\DuplexStreamInterface;

final class ConnectionFactory
{
    /**
     * Creates the connection for the given stream.
     *
     * @param DuplexStreamInterface $stream
     * @return ConnectionInterface
     */
    public static function createConnection(DuplexStreamInterface $stream): ConnectionInterface
    {
        return new StreamConnection($stream);
    }
}