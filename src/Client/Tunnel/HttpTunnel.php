<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Tunnel;

class HttpTunnel extends Tunnel
{
    protected $hosts;

    public function __construct($protocol, $remotePort, $hosts)
    {
        parent::__construct($protocol, $remotePort);
        $this->hosts = $hosts;
    }

    /**
     * @return mixed
     */
    public function getHosts()
    {
        return $this->hosts;
    }
}