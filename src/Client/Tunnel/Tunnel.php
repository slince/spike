<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Tunnel;

use React\Socket\ConnectionInterface;

abstract class Tunnel implements TunnelInterface
{
    /**
     * The control connection
     * @var ConnectionInterface
     */
    protected $controlConnection;

    /**
     * The tunnel connection
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The remote port
     * @var int
     */
    protected $remotePort;

    public function __construct($remotePort, ConnectionInterface $controlConnection = null)
    {
        $this->remotePort = $remotePort;
        $this->controlConnection = $controlConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getControlConnection()
    {
        return $this->controlConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function setControlConnection(ConnectionInterface $connection)
    {
        $this->controlConnection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemotePort()
    {
        return $this->remotePort;
    }

    /**
     * {@inheritdoc}
     */
    public function pipe(ConnectionInterface $connection)
    {
        $connection->pipe($this->connection);
        $this->connection->pipe($connection);
    }
}