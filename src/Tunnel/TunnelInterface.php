<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Tunnel;

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
     * Checks whether the tunnel is active
     * @return boolean
     */
    public function isActive();

    /**
     * Pipes the proxy connection to the tunnel
     * @param ConnectionInterface $proxyConnection
     */
    public function pipe(ConnectionInterface $proxyConnection);

    /**
     * Gets the tunnel server port
     * @return int
     */
    public function getServerPort();

    /**
     * Checks whether the tunnel match the info
     * @param array $info
     * @return boolean
     */
    public function match($info);

    /**
     * Gets the tunnel info
     * @return array
     */
    public function toArray();

    /**
     * Push the buffer
     * @param string $data
     */
    public function pushBuffer($data);

    /**
     * Get the buffer
     * @return string
     */
    public function getBuffer();
}