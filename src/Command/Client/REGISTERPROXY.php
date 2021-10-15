<?php

namespace Spike\Command\Client;

use Spike\Command\AbstractCommand;
use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class REGISTERPROXY extends AbstractCommand
{
    public function __construct(int $serverPort, string $clientId)
    {
        parent::__construct(['server_port' => $serverPort, 'client_id' => $clientId]);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static(
            $message->getArgument('server_port'),
            $message->getArgument('client_id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandId(): string
    {
        return 'REGISTERPROXY';
    }
}