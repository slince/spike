<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Tunnel;

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
    protected $serverPort;

    /**
     * @var string
     */
    protected $buffer;

    public function __construct($serverPort, ConnectionInterface $controlConnection = null)
    {
        $this->serverPort = $serverPort;
        $this->controlConnection = $controlConnection;
    }


    /**
     * {@inheritdoc}
     */
    public function getServerPort()
    {
        return $this->serverPort;
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
        return $this->getServerPort() == $info['serverPort'];
    }

    /**
     * {@inheritdoc}
     */
    public function pushBuffer($data)
    {
        $this->buffer .= $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuffer()
    {
        return $this->buffer;
    }
}