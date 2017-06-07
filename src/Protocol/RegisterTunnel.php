<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Spike\Tunnel\Tunnel;
use Spike\Tunnel\TunnelFactory;

class RegisterTunnel extends SpikeRequest
{
    /**
     * @var Tunnel
     */
    protected $tunnel;

    public function __construct(Tunnel $tunnel, array $headers = [])
    {
        $this->tunnel = $tunnel;
        parent::__construct('register_tunnel', $headers);
    }

    /**
     * @return Tunnel
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }

    /**
     * @param Tunnel $tunnel
     */
    public function setTunnel($tunnel)
    {
        $this->tunnel = $tunnel;
    }

    public static function parseBody($body)
    {
        return TunnelFactory::fromArray(json_decode($body, true));
    }

    public function getBody()
    {
        return json_encode($this->tunnel->toArray());
    }
}