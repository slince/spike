<?php

namespace Spike\Server\Connection;

use Evenement\EventEmitter;
use React\Socket\ConnectionInterface;
use React\Stream\Util;

class PublicConnection extends EventEmitter
{
    const WAITING = 1;
    const WORKING = 2;
    const PAUSED = 3;

    /**
     * Connection status
     *
     * @var int
     */
    protected $status = self::WAITING;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var ProxyConnection
     */
    protected $proxyConnection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        Util::forwardEvents($connection, $this, ['close']);
    }

    public function pause()
    {
        $this->status = static::PAUSED;
        $this->connection->pause();
    }

    public function resume()
    {
        $this->status = static::WAITING;
        $this->connection->resume();
    }

    public function work()
    {
        if ($this->status !== self::WAITING) {
            throw new \LogicException('Cannot work a connection that is not in waiting state');
        }

        $this->status = static::WORKING;
    }

    /**
     * @param ProxyConnection $proxyConnection
     */
    public function setProxyConnection(ProxyConnection $proxyConnection)
    {
        $this->proxyConnection = $proxyConnection;
    }

    /**
     * @return ProxyConnection
     */
    public function getProxyConnection(): ?ProxyConnection
    {
        return $this->proxyConnection;
    }

    /**
     * @return ConnectionInterface
     */
    public function getRawConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}