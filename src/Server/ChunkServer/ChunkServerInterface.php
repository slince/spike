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

use Spike\Common\Tunnel\TunnelInterface;
use Spike\Server\Client;

interface ChunkServerInterface
{
    /**
     * Gets the tunnel
     *
     * @return TunnelInterface
     */
    public function getTunnel();

    /**
     * @return Client
     */
    public function getClient();

    /**
     * Run the server
     */
    public function run();

    /**
     * Close the server
     */
    public function close();
}