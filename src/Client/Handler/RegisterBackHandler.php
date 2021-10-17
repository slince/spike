<?php

namespace Spike\Client\Handler;

use Spike\Command\CommandInterface;
use Spike\Connection\ConnectionInterface;
use Spike\Server\Command\REGISTERBACK;

class RegisterBackHandler extends ClientCommandHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(CommandInterface $command, ConnectionInterface $connection)
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function getSubscribedCommands(): array
    {
        return [REGISTERBACK::class];
    }
}