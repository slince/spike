<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Tunnel;

use React\Socket\ConnectionInterface;

class Tunnel implements TunnelInterface
{
    protected $connection;

    protected $active;

    protected $port;

    protected $localUri;

    public function __construct($port, ConnectionInterface $connection = null)
    {
        $this->connection = $connection;
        $this->port = $port;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function open()
    {
        return $this;
    }

    public function close()
    {
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    public function pipe(ConnectionInterface $connection)
    {
        $connection->pipe($this->connection);
    }
}