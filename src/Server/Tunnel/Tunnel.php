<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Tunnel;

use React\Socket\ConnectionInterface;
use Spike\Exception\InvalidArgumentException;

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
     * The proxy connection
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

    /**
     * @var string
     */
    protected $data;

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
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
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
     * @param ConnectionInterface $proxyConnection
     */
    public function setProxyConnection($proxyConnection)
    {
        $this->proxyConnection = $proxyConnection;
    }

    /**
     * @return ConnectionInterface
     */
    public function getProxyConnection()
    {
        return $this->proxyConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function pipe(ConnectionInterface $proxyConnection)
    {
        $this->proxyConnection = $proxyConnection;
        if ($this->connection) {
            $this->connection->pipe($proxyConnection);
            $proxyConnection->pipe($this->connection);
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