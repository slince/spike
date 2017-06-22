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
    public function registerProxyConnection(ConnectionInterface $connection, SpikeInterface $message);

    /**
     * Gets all public connection of the tunnel server
     * @return PublicConnectionCollection
     */
    public function getPublicConnections();

    /**
     * Close the given public connection
     * @param PublicConnection $publicConnection
     * @param null $message
     */
    public function closePublicConnection(PublicConnection $publicConnection, $message = null);

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