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
     * The supported protocol of the tunnel
     * @var string
     */
    protected $protocol;

    /**
     * The remote port
     * @var int
     */
    protected $remotePort;

    public function __construct($protocol, $remotePort)
    {
        $this->protocol = $protocol;
        $this->remotePort = $remotePort;
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
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return int
     */
    public function getRemotePort()
    {
        return $this->remotePort;
    }
}