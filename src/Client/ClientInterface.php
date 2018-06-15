<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Client;

use React\Socket\ConnectionInterface;

interface ClientInterface
{
    /**
     * Starts the client
     */
    public function start();

    /**
     * Closes the client
     */
    public function close();

    /**
     * Gets the client id given by server.
     *
     * @return string
     */
    public function getId();

    /**
     * Sets client ID.
     *
     * @param string $id
     */
    public function setId($id);

    /**
     * Gets the last active time.
     *
     * @return \DateTimeInterface
     */
    public function getActiveAt();

    /**
     * Gets the control connection with server.
     *
     * @return ConnectionInterface
     */
    public function getControlConnection();
}