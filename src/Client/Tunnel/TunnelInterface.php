<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Tunnel;

use React\Socket\ConnectionInterface;

interface TunnelInterface
{
    /**
     * Gets the control connection
     * @return ConnectionInterface
     */
    public function getControlConnection();

    /**
     * Sets the control connection
     * @param ConnectionInterface $connection
     */
    public function setControlConnection(ConnectionInterface $connection);

    /**
     * Gets the tunnel connection
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * Sets the tunnel connection
     * @param ConnectionInterface $connection
     */
    public function setConnection(ConnectionInterface $connection);

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