<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Common\Tunnel;

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