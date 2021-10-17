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

namespace Spike\Client\Connection;

use Spike\Connection\ConnectionInterface;

final class ProxyConnection
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }


    public function pipe(LocalConnection $dest)
    {
        $dest->setProxyConnection($this);
        $src = $this->connection->getStream();
        $dst = $dest->getRawConnection();
        $src->pipe($dst);
        $dst->pipe($src, ['end' => false]);
    }
}