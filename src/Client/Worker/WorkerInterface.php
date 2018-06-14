<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Client\Worker;

use Spike\Common\Tunnel\TunnelInterface;

interface WorkerInterface
{
    /**
     * Starts work.
     */
    public function start();

    /**
     * Stop work.
     */
    public function stop();

    /**
     * gets tunnel which the worker for.
     *
     * @return TunnelInterface
     */
    public function getTunnel();

    /**
     * Get target host
     * return string.
     */
    public function resolveTargetHost();

    /**
     * Checks whether the worker support the tunnel.
     *
     * @param TunnelInterface $tunnel
     *
     * @return boolean
     */
    public static function support(TunnelInterface $tunnel);
}