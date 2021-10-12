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
}