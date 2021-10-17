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

namespace Spike\Server\Connection;

final class PublicConnectionPool implements \Countable, \IteratorAggregate
{
    /**
     * @var PublicConnection[]|\SplQueue
     */
    protected $connections;

    public function __construct(array $connections = [])
    {
        $this->connections = new \SplQueue();
        foreach ($connections as $connection) {
            $this->add($connection);
        }
    }

    /**
     * Add connection to the pool.
     *
     * @param PublicConnection $connection
     */
    public function add(PublicConnection $connection)
    {
        $this->connections->enqueue($connection);
    }

    /**
     * Consumes a public connection.
     *
     * @return PublicConnection|null
     */
    public function consume(): ?PublicConnection
    {
        if ($this->connections->isEmpty()) {
            return null;
        }
        return $this->connections->dequeue();
    }

    /**
     * Checks whether the pool is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->connections->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->connections);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->connections);
    }
}