<?php

namespace Spike\Server;

final class TunnelListener
{
    /**
     * @var Tunnel
     */
    protected $tunnel;

    public function __construct(Tunnel $tunnel)
    {
        $this->tunnel = $tunnel;
    }


}