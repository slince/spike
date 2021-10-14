<?php

namespace Spike\Server\Command;

use Spike\Command\AbstractCommand;
use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class REQUESTPROXY extends AbstractCommand
{
    public function __construct(int $port)
    {
        parent::__construct(['server_port' => $port]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandId(): string
    {
        return 'REQUESTPROXY';
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static($message->getArgument('server_port'));
    }
}