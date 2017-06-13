<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\TunnelClient;

use React\Socket\ConnectionInterface;
use Spike\Client\Tunnel\TunnelInterface;

interface TunnelClientInterface
{
    /**
     * Gets the tunnel
     * @return TunnelInterface
     */
    public function getTunnel();

    /**
     * Handles the local connection
     * @param ConnectionInterface $connection
     */
    public function handleLocalConnection(ConnectionInterface $connection);
}