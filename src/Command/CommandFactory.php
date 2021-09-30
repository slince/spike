<?php

namespace Spike\Command;

use Spike\Exception\BadMessageException;
use Spike\Protocol\Message;

final class CommandFactory
{
    protected $commandMap = [
        'REGISTER' => Client\REGISTER::class
    ];

    /**
     * Creates the command from message.
     *
     * @param Message $message
     * @return CommandInterface
     */
    public function createCommand(Message $message): CommandInterface
    {
        $commandId = $message->getArgument('_cid_');
        if (!isset($this->commandMap[$commandId])) {
            throw new BadMessageException('Cannot find the command id from the message');
        }
        return call_user_func([$this->commandMap[$commandId], 'fromMessage'], $message);
    }
}