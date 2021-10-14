<?php

namespace Spike\Socket;

use React\EventLoop\LoopInterface;
use React\Socket\SecureServer;
use React\Socket\TcpServer;
use React\Socket\UnixServer;

class SocketServer extends AbstractServer
{
    protected function createSocket(string $address, LoopInterface $loop)
    {
        // apply default options if not explicitly given
        $context += array(
            'tcp' => array(),
            'tls' => array(),
            'unix' => array()
        );

        $scheme = 'tcp';
        $pos = \strpos($address, '://');
        if ($pos !== false) {
            $scheme = \substr($address, 0, $pos);
        }

        if ($scheme === 'unix') {
            $server = new UnixServer($address, $loop, $context['unix']);
        } else {
            if (preg_match('#^(?:\w+://)?\d+$#', $address)) {
                throw new \InvalidArgumentException('Invalid URI given');
            }

            $server = new TcpServer(str_replace('tls://', '', $address), $loop, $context['tcp']);

            if ($scheme === 'tls') {
                $server = new SecureServer($server, $loop, $context['tls']);
            }
        }

    }
}