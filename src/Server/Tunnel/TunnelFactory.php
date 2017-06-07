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
        if ($data['protocol'] == 'tcp') {
            $tunnel = new HttpTunnel($data['remoteIp'], $data['hosts']);
        } else {
            $tunnel = new TcpTunnel($data['remoteIp'], $data['host']);
        }
        return $tunnel;
    }
}