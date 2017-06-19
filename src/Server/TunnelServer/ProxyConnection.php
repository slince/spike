<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\Socket\ConnectionInterface;
use React\Stream\DuplexStreamInterface;

class ProxyConnection
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var string
     */
    protected $initBuffer;

    /**
     * @var string
     */
    protected $id;

    public function __construct(ConnectionInterface $connection, $initBuffer = '')
    {
        $this->connection =  $connection;
        $this->initBuffer = $initBuffer;
        $this->id = spl_object_hash($connection);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $initBuffer
     */
    public function setInitBuffer($initBuffer)
    {
        $this->initBuffer = $initBuffer;
    }

    /**
     * @return string
     */
    public function getInitBuffer()
    {
        return $this->initBuffer;
    }

    public function pause()
    {
        $this->connection->pause();
    }

    public function resume()
    {
        $this->connection->resume();
    }
}