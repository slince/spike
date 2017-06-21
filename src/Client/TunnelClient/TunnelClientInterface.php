<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\TunnelClient;

use React\Socket\ConnectionInterface;
use Spike\Tunnel\TunnelInterface;

interface TunnelClientInterface
{
    /**
     * Gets the tunnel
     * @return TunnelInterface
     */
    public function getTunnel();

    /**
     * Run the tunnel client
     */
    public function run();

    /**
     * Handles the local connection
     * @param ConnectionInterface $connection
     */
    public function handleLocalConnection(ConnectionInterface $connection);
}