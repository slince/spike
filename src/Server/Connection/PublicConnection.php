<?php

namespace Spike\Server\Connection;

use React\Socket\ConnectionInterface;

class PublicConnection
{
    const WORKING = 1;
    const WAITING = 2;
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
        $this->status = static::WORKING;
        $this->connection->resume();
    }

    /**
     * @return ConnectionInterface
     */
    public function getRawConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}