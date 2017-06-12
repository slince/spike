<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Tunnel;

use React\Socket\ConnectionInterface;

class Tunnel implements TunnelInterface
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
     * @var boolean
     */
    protected $active;

    /**
     * The tunnel server port
     * @var int
     */
    protected $port;

    public function __construct($port, ConnectionInterface $controlConnection = null)
    {
        $this->controlConnection = $controlConnection;
        $this->port = $port;
    }


    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->active;
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
    public function pipe(ConnectionInterface $connection)
    {
        $connection->pipe($this->connection);
    }
}