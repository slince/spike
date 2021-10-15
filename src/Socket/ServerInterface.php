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