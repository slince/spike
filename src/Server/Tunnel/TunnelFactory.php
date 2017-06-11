<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Tunnel;

class TunnelFactory
{
    public static function fromArray($data)
    {
        if ($data['protocol'] == 'http') {
            $tunnel = new HttpTunnel($data['remotePort'], $data['proxyHosts']);
        } else {
            $tunnel = new TcpTunnel($data['remotePort'], $data['host']);
        }
        return $tunnel;
    }
}