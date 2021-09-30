<?php

namespace Spike\Socket;

use React\Socket\ConnectionInterface;

class TcpServer extends AbstractServer
{
    /**
     * @internal
     * @param ConnectionInterface $connection
     */
    public function handleConnection(ConnectionInterface $connection)
    {
        $this->emit('connection', [$connection]);
    }
}