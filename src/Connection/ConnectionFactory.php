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