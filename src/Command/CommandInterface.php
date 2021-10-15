<?php

declare(strict_types=1);

namespace Spike\Command;

use Spike\Protocol\Message;

interface CommandInterface
{
    /**
     * Gets the command id.
     *
     * @return string
     */
    public function getCommandId(): string;

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