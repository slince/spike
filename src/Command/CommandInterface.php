<?php

namespace Spike\Command;

use Spike\Protocol\Message;

interface CommandInterface
{
    /**
     * Returns the command arguments.
     *
     * @return array
     */
    public function getArguments(): array;

    /**
     * Return the given argument.
     *
     * @param string $name
     * @return mixed
     */
    public function getArgument(string $name);

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