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

use React\Socket\ConnectionInterface as RawConnection;

final class LocalConnection
{
    /**
     * @var RawConnection
     */
    protected $connection;

    /**
     * @var ProxyConnection
     */
    protected $proxyConnection;

    public function __construct(RawConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return RawConnection
     */
    public function getRawConnection(): RawConnection
    {
        return $this->connection;
    }

    /**
     * @param ProxyConnection $proxyConnection
     */
    public function setProxyConnection(ProxyConnection $proxyConnection)
    {
        $this->proxyConnection = $proxyConnection;
    }

    /**
     * @return ProxyConnection
     */
    public function getProxyConnection(): ProxyConnection
    {
        return $this->proxyConnection;
    }
}