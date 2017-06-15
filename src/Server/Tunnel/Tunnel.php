<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Tunnel;

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
     * The proxy connection that need to be handled
     * @var ConnectionInterface
     */
    protected $proxyConnection;

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
        if ($this->proxyConnection) {
            $this->proxyConnection->pipe($this->connection);
            $this->connection->pipe($this->proxyConnection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function pipe(ConnectionInterface $proxyConnection)
    {
        $this->proxyConnection = $proxyConnection;
        if ($this->connection) {
            $proxyConnection->pipe($this->connection);
            $this->connection->pipe($proxyConnection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function match($info)
    {
        return $this->getPort() == $info['remotePort'];
    }
}