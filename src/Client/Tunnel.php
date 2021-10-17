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

use Spike\Exception\InvalidArgumentException;

final class Tunnel
{
    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var int
     */
    protected $serverPort;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $localPort;

    /**
     * @var string
     */
    protected $dsn;

    /**
     * @var int
     */
    protected $maxWorkers = 4;

    /**
     * @var int
     */
    protected $proxyPoolSize = 10;

    public function __construct(string $dsn, int $serverPort)
    {
        $this->setDsn($dsn);
        $this->serverPort = $serverPort;
    }

    /**
     * @param string $dsn
     */
    public function setDsn(string $dsn)
    {
        $parsedDsn = parse_url($dsn);
        if (!isset($parsedDsn['scheme'])) {
            throw new InvalidArgumentException(sprintf('The "%s" DSN must contain a scheme.', $dsn));
        }
        $this->scheme = $parsedDsn['scheme'];

        if (!isset($parsedDsn['host'])) {
            throw new InvalidArgumentException(sprintf('The "%s" DSN must contain a host (use "tcp" by default).', $dsn));
        }
        $this->host = $parsedDsn['host'];

        if (!isset($parsedDsn['port'])) {
            throw new InvalidArgumentException(sprintf('The "%s" DSN must contain a port.', $dsn));
        }
        $this->localPort = $parsedDsn['port'];
        $this->dsn = $dsn;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getLocalPort(): int
    {
        return $this->localPort;
    }

    public function getServerPort(): int
    {
        return $this->serverPort;
    }

    /**
     * @return string
     */
    public function getDsn(): string
    {
        return $this->dsn;
    }

    /**
     * @param int $maxWorkers
     */
    public function setMaxWorkers(int $maxWorkers)
    {
        $this->maxWorkers = $maxWorkers;
    }

    /**
     * @return int
     */
    public function getMaxWorkers(): int
    {
        return $this->maxWorkers;
    }

    /**
     * @param int $proxyPoolSize
     */
    public function setProxyPoolSize(int $proxyPoolSize)
    {
        $this->proxyPoolSize = $proxyPoolSize;
    }

    /**
     * @return int
     */
    public function getProxyPoolSize(): int
    {
        return $this->proxyPoolSize;
    }
}