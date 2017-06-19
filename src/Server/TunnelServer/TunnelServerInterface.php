<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\Socket\ConnectionInterface;
use Spike\Protocol\Spike;
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
     * @param Spike $spike
     */
    public function registerTunnelConnection(ConnectionInterface $connection, Spike $spike);

    /**
     * Run the server
     */
    public function run();

    /**
     * Pause the server
     */
    public function pause();

    /**
     * Resumes the server
     */
    public function resume();
}