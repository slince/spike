<?php

namespace Spike;

use Spike\Exception\InvalidArgumentException;

final class Tunnel
{
    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $localPort;

    /**
     * @var int
     */
    protected $serverPort;

    /**
     * @var string
     */
    protected $originalDsn;

    public function __construct(string $dsn, int $serverPort)
    {
        $this->originalDsn = $dsn;
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
        $this->serverPort = $serverPort;
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
}