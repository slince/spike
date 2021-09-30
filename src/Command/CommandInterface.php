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
}