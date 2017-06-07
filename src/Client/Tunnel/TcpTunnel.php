<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Tunnel;

class TcpTunnel extends Tunnel
{
    protected $localHost;

    public function __construct($protocol, $remotePort, $localHost)
    {
        parent::__construct($protocol, $remotePort);
        $this->localHost = $localHost;
    }

    /**
     * @return string
     */
    public function getLocalHost()
    {
        return $this->localHost;
    }
}