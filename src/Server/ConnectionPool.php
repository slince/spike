<?php

namespace Spike\Server;

final class ConnectionPool implements \Countable, \IteratorAggregate
{
    /**
     * @var ProxyConnection[]
     */
    protected $connections;

    public function __construct(array $connections = [])
    {
        $this->connections = $connections;
    }

    /**
     * Add connection to the pool.
     *
     * @param ProxyConnection $connection
     */
    public function add(ProxyConnection $connection)
    {
        $this->connections[] = $connection;
    }

    /**
     * Try get a ready connection.
     *
     * @return ProxyConnection|null
     */
    public function tryGet(): ?ProxyConnection
    {
        $connections = array_filter($this->connections, function (ProxyConnection $connection) {
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