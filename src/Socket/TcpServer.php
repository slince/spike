<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Socket;

use React\EventLoop\LoopInterface;
use React\Socket\SecureServer;
use React\Socket\TcpServer as SocketServer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TcpServer extends AbstractServer
{

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'tcp_context' => [],
            'tls' => false,
            'tls_context' => []
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createSocket(string $address, LoopInterface $loop)
    {
        $server = new SocketServer($address, $loop, $this->options['tcp_context']);
        if ($this->options['tls']) {
            $server = new SecureServer($server, $loop, $this->options['tls_context']);
        }
        return $server;
    }
}