<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Tunnel;

abstract class Tunnel implements TunnelInterface
{
    /**
     * The supported protocol of the tunnel
     * @var string
     */
    protected $protocol;

    /**
     * The remote port
     * @var int
     */
    protected $remotePort;

    public function __construct($protocol, $remotePort)
    {
        $this->protocol = $protocol;
        $this->remotePort = $remotePort;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return int
     */
    public function getRemotePort()
    {
        return $this->remotePort;
    }
}