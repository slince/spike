<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Tunnel;

class HttpTunnel extends Tunnel
{
    protected $hosts;

    public function __construct($remotePort, $hosts)
    {
        parent::__construct(static::TUNNEL_HTTP, $remotePort);
        $this->hosts = $hosts;
    }

    /**
     * @return mixed
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    public function toArray()
    {
        return [
            'protocol' => $this->protocol,
            'remotePort' => $this->remotePort,
            'hosts' => $this->hosts,
        ];
    }
}