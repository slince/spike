<?php

declare(strict_types=1);

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
    public static function wrapConnection(DuplexStreamInterface $stream): ConnectionInterface
    {
        return new StreamConnection($stream);
    }
}