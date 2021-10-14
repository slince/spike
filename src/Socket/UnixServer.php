<?php

namespace Spike\Socket;

use React\EventLoop\LoopInterface;
use React\Socket\UnixServer as SocketServer;

class UnixServer extends AbstractServer
{
    protected function createSocket(string $address, LoopInterface $loop)
    {
        return new SocketServer($address, $loop, $this->createSocketContext());
    }

    protected function createSocketContext(): array
    {
        return [];
    }
}