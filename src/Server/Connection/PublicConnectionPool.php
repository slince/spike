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
     * @var PublicConnection[]
     */
    protected $connections;

    public function __construct(array $connections = [])
    {
        $this->connections = $connections;
    }

    /**
     * Add connection to the pool.
     *
     * @param PublicConnection $connection
     */
    public function add(PublicConnection $connection)
    {
        $this->connections[] = $connection;
    }

    /**
     * Try get a ready connection.
     *
     * @return PublicConnection|null
     */
    public function tryGet(): ?PublicConnection
    {
        $connections = array_filter($this->connections, function (PublicConnection $connection) {
            return $connection->isReady();
        });
        if (count($connections) > 0) {
            $connection = $connections[0];
            $connection->occupy();
        }
        return null;
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