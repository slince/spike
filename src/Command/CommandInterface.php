<?php

namespace Spike\Command;

use Spike\Protocol\Message;

interface CommandInterface
{
    /**
     * Create the message instance.
     *
     * @return Message
     */
    public function createMessage(): Message;

    /**
     * Create command base on given message.
     *
     * @param Message $message
     * @return static
     */
    public static function fromMessage(Message $message): CommandInterface;
}