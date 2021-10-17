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

namespace Spike\Client;

final class TunnelCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Tunnel[]
     */
    protected $tunnels;

    public function __construct(array $tunnels = [])
    {
        $this->tunnels = $tunnels;
    }

    /**
     * Gets the listener for the given port.
     *
     * @param int $port
     * @return Tunnel|null
     */
    public function get(int $port): ?Tunnel
    {
        return $this->tunnels[$port] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->tunnels);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->tunnels);
    }
}