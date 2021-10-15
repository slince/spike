<?php

namespace Spike\Server;

class TunnelListenerCollection implements \IteratorAggregate
{
    /**
     * @var TunnelListener[]
     */
    protected $listeners;

    public function __construct(array $listeners = [])
    {
        $this->listeners = $listeners;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->listeners);
    }

    /**
     * Gets the listener for the given port.
     *
     * @param int $port
     * @return TunnelListener|null
     */
    public function get(int $port): ?TunnelListener
    {
        return $this->listeners[$port] ?? null;
    }
}