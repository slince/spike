<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Common\Tunnel;

interface TunnelInterface extends \JsonSerializable
{
    /**
     * Gets the tunnel server port.
     *
     * @return int
     */
    public function getServerPort();

    /**
     * Gets the tunnel protocol.
     *
     * @return string
     */
    public function getProtocol();

    /**
     * Gets the tunnel info.
     *
     * @return array
     */
    public function toArray();

    /**
     * Get the summary of the tunnel.
     *
     * @return string
     */
    public function __toString();

    /**
     * Checks whether the tunnel match the info.
     *
     * @param array $info
     *
     * @return boolean
     */
    public function match($info);
}