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

    /**
     * The create time
     * @var int
     */
    protected $createAt;

    public function __construct(ConnectionInterface $connection, $initBuffer = '')
    {
        $this->connection =  $connection;
        $this->initBuffer = $initBuffer;
        $this->id = spl_object_hash($connection);
        $this->createAt = microtime(true);
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

    /**
     * Pauses the connection
     */
    public function pause()
    {
        $this->connection->pause();
    }

    /**
     * Resumes the connection
     */
    public function resume()
    {
        $this->connection->resume();
    }

    /**
     * Gets the waiting time of the connection
     * @return float
     */
    public function getWaitingTime()
    {
        return microtime(true) - $this->createAt;
    }
}