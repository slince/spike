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

namespace Spike\Handler;

use Spike\Command\CommandInterface;
use Spike\Connection\ConnectionInterface;

interface HandlerInterface
{
    /**
     * Handling the command.
     *
     * @param CommandInterface $command
     * @param ConnectionInterface $connection
     */
    public function handle(CommandInterface $command, ConnectionInterface $connection);

    /**
     * Returns whether this class supports the given command.
     *
     * @param CommandInterface $command
     * @return bool
     */
    public function supports(CommandInterface $command): bool;
}