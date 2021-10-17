<?php

namespace Spike\Server\Connection;

use Evenement\EventEmitter;
use React\Socket\ConnectionInterface as RawConnection;
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
     * @var RawConnection
     */
    protected $connection;

    /**
     * @var ProxyConnection
     */
    protected $proxyConnection;

    public function __construct(RawConnection $connection)
    {
        $this->connection = $connection;
        Util::forwardEvents($connection, $this, ['close']);
    }

    public function close()
    {
        $this->connection->close();
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
     * @return RawConnection
     */
    public function getRawConnection(): RawConnection
    {
        return $this->connection;
    }
}