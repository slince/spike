<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\Socket\ConnectionInterface;
use Spike\Protocol\SpikeInterface;
use Spike\Timer\TimerInterface;
use Spike\Tunnel\TunnelInterface;

interface TunnelServerInterface
{
    /**
     * Gets the tunnel
     * @return TunnelInterface
     */
    public function getTunnel();

    /**
     * Gets the control connection
     * @return ConnectionInterface
     */
    public function getControlConnection();

    /**
     * Registers the tunnel connection
     * @param ConnectionInterface $connection
     * @param SpikeInterface $message
     */
    public function registerTunnelConnection(ConnectionInterface $connection, SpikeInterface $message);

    /**
     * Gets all proxy connection of the tunnel server
     * @return ProxyConnectionCollection
     */
    public function getProxyConnections();

    /**
     * Close the given proxy connection
     * @param ProxyConnection $proxyConnection
     * @param null $message
     */
    public function closeProxyConnection(ProxyConnection $proxyConnection, $message = null);

    /**
     * Add one timer
     * @param TimerInterface $timer
     */
    public function addTimer(TimerInterface $timer);

    /**
     * Run the server
     */
    public function run();

    /**
     * Close the server
     */
    public function close();
}