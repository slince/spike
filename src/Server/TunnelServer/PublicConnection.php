<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\Socket\ConnectionInterface;

/**
 * @method write($data)
 * @method on($eventName, callable $listener)
 * @method pipe($dst, array $options = []);
 * @method removeListener($eventName, callable $listener);
 * @method removeAllListeners($eventName = null);
 * @method end($data = null);
 * @method pause();
 * @method resume();
 */
class PublicConnection
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
     * @codeCoverageIgnore
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->connection, $name], $arguments);
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
     * Gets the waiting duration of the connection
     * @return float
     */
    public function getWaitingDuration()
    {
        return microtime(true) - $this->createAt;
    }
}