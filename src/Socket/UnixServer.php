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
use React\Socket\UnixServer as SocketServer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnixServer extends AbstractServer
{
    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'unix_context' => [],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createSocket(string $address, LoopInterface $loop)
    {
        return new SocketServer($address, $loop, $this->options['unix_context']);
    }
}