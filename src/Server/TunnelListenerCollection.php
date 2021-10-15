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

final class TunnelListenerCollection implements \IteratorAggregate, \Countable
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
     * Gets the listener for the given port.
     *
     * @param int $port
     * @return TunnelListener|null
     */
    public function get(int $port): ?TunnelListener
    {
        return $this->listeners[$port] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->listeners);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->listeners);
    }
}