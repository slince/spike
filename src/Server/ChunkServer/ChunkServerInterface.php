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

use Doctrine\Common\Collections\Collection;
use React\Socket\ConnectionInterface;
use Spike\Client\ClientInterface;
use Spike\Common\Tunnel\TunnelInterface;
use Spike\Server\ServerInterface;

interface ChunkServerInterface
{
    /**
     * Gets server.
     *
     * @return ServerInterface
     */
    public function getServer();

    /**
     * Gets the tunnel.
     *
     * @return TunnelInterface
     */
    public function getTunnel();

    /**
     * Gets client.
     *
     * @return ClientInterface
     */
    public function getClient();

    /**
     * Run the server.
     */
    public function start();

    /**
     * Close the server.
     */
    public function stop();

    /**
     * Pipe proxy connection to the chunk server.
     *
     * @param int                 $publicConnectionId
     * @param ConnectionInterface $proxyConnection
     */
    public function setProxyConnection($publicConnectionId, ConnectionInterface $proxyConnection);

    /**
     * Gets all public connections.
     *
     * @return Collection|PublicConnection[]
     */
    public function getPublicConnections();

    /**
     * Close public connection.
     *
     * @param PublicConnection $publicConnection
     * @param string|null      $message
     */
    public function closePublicConnection(PublicConnection $publicConnection, $message = null);
}