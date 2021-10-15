<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server;

use Spike\Connection\ConnectionInterface;
use React\Socket\ConnectionInterface as RawConnection;

class ProxyConnection
{
    const READY = 1;
    const BUSY = 2;
    const CLOSED = 3;
    const LOCKED = 4;

    /**
     * Number of handled requests
     *
     * @var int
     */
    private $handledRequests = 0;

    /**
     * Connection status
     *
     * @var int
     */
    protected $status = self::READY;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function isReady(): bool
    {
        return static::READY === $this->status;
    }

    /**
     * Ready a connection after bootstrap completed
     *
     * @return void
     */
    public function ready()
    {
        $this->status = self::READY;
    }

    /**
     * Occupies a connection for request handling
     *
     * @return void
     */
    public function occupy()
    {
        if ($this->status !== self::READY) {
            throw new \LogicException('Cannot occupy a connection that is not in ready state');
        }

        $this->status = self::BUSY;
    }

    /**
     * Releases a connection from request handling
     *
     * @return void
     */
    public function release()
    {
        if ($this->status !== self::BUSY) {
            throw new \LogicException('Cannot release a connection that is not in busy state');
        }

        $this->status = self::READY;
        $this->handledRequests++;
    }

    public function pipe(RawConnection $dest)
    {
        $this->connection->getStream()->pipe($dest);
        $dest->pipe($this->connection->getStream(), [
            'end' => false
        ]);
    }

    public function turnoff(RawConnection $dest)
    {

    }
}