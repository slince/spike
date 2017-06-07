<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Tunnel;

class TunnelFactory
{
    public static function fromArray($data)
    {
        if ($data['protocol'] == TunnelInterface::TUNNEL_HTTP) {
            $tunnel = new HttpTunnel($data['remoteIp'], $data['hosts']);
        } else {
            $tunnel = new TcpTunnel($data['remoteIp'], $data['host']);
        }
        return $tunnel;
    }
}