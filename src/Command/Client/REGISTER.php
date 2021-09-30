<?php

namespace Spike\Command\Client;

use Spike\Command\AbstractCommand;
use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

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

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        $tunnelParameters = $message->getArgument('tunnels');
        $tunnels = [];
        return new REGISTER($message->getArgument('username'), $message->getArgument('password'), $tunnels);
    }
}