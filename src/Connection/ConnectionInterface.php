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

use Evenement\EventEmitterInterface;
use Spike\Command\CommandInterface;

interface ConnectionInterface extends EventEmitterInterface
{
    /**
     * Returns the full remote address (URI) where this connection has been established with.
     *
     * @return string
     */
    public function getRemoteAddress(): string;

    /**
     * Returns the full local address (full URI with scheme, IP and port) where this connection has been established with.
     *
     * @return string
     */
    public function getLocalAddress(): string;

    /**
     * Closes the connection to Spiked.
     *
     * @param bool $force
     */
    public function disconnect(bool $force = false);

    /**
     * Writes a request for the given command over the connection.
     *
     * @param CommandInterface $command Command instance.
     */
    public function executeCommand(CommandInterface $command);
}