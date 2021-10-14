<?php

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