<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\ChunkServer;

use React\Socket\ConnectionInterface;
use Spike\Common\Tunnel\TunnelInterface;

interface ChunkServerInterface
{
    /**
     * Gets the tunnel
     *
     * @return TunnelInterface
     */
    public function getTunnel();

    /**
     * Run the server
     */
    public function start();

    /**
     * Close the server
     */
    public function stop();

    /**
     * Pipe proxy connection to the chunk server
     *
     * @param int $publicConnectionId
     * @param ConnectionInterface $proxyConnection
     */
    public function setProxyConnection($publicConnectionId, ConnectionInterface $proxyConnection);
}