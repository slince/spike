<?php

namespace Spike\Command\Client;

use Spike\Command\AbstractCommand;

class REGISTER extends AbstractCommand
{
    public function __construct(string $username, string $password, array $tunnels)
    {
        parent::__construct(['username' => $username, 'password' => $password, 'tunnels' => $tunnels]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandId(): string
    {
        return 'REGISTER';
    }
}