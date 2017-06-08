<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Tunnel;

class TcpTunnel extends Tunnel
{
    protected $host;

    public function __construct($remotePort, $host)
    {
        parent::__construct(static::TUNNEL_TCP, $remotePort);
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    public function toArray()
    {
        return [
            'protocol' => $this->protocol,
            'remotePort' => $this->remotePort,
            'host' => $this->host,
        ];
    }
}