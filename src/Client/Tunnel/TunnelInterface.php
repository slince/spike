<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Tunnel;

interface TunnelInterface
{
    const TUNNEL_TCP = 'tcp';

    const TUNNEL_HTTP = 'http';

    /**
     * Get the tunnel information
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function getRemotePort();
}