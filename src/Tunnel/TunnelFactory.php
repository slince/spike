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
        if ($data['protocol'] == 'http') {
            $tunnel = new HttpTunnel($data['serverPort'], $data['proxyHosts']);
        } else {
            $tunnel = new TcpTunnel($data['serverPort'], $data['host']);
        }
        return $tunnel;
    }
}