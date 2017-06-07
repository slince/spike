<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;


class RegisterTunnel extends SpikeRequest
{
    /**
     * @var array
     */
    protected $tunnel;

    public function __construct(array $tunnel, array $headers = [])
    {
        $this->tunnel = $tunnel;
        parent::__construct('register_tunnel', $headers);
    }

    /**
     * @return array
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }

    /**
     * @param array $tunnel
     */
    public function setTunnel($tunnel)
    {
        $this->tunnel = $tunnel;
    }

    public static function parseBody($body)
    {
        return json_decode($body, true);
    }

    public function getBody()
    {
        return json_encode($this->tunnel);
    }
}