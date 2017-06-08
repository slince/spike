<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Tunnel;

class TunnelFactory
{
    public static function fromArray($data)
    {
        if ($data['protocol'] == TunnelInterface::TUNNEL_HTTP) {
            $tunnel = new HttpTunnel($data['remotePort'], $data['hosts']);
        } else {
            $tunnel = new TcpTunnel($data['remotePort'], $data['host']);
        }
        return $tunnel;
    }
}