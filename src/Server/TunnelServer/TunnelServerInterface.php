<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\Socket\ConnectionInterface;
use Spike\Protocol\SpikeInterface;
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
     * Run the server
     */
    public function run();

    /**
     * Close the server
     */
    public function close();
}