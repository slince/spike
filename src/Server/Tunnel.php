<?php

namespace Spike\Server;

final class Tunnel
{
    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var int
     */
    protected $port;

    public function __construct(string $scheme, int $port)
    {
        $this->scheme = $scheme;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }
}