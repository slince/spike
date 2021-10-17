<?php

namespace Spike\Server\Connection;

use React\Socket\ConnectionInterface;

class PublicConnection
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

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
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
     * @return ConnectionInterface
     */
    public function getRawConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}