<?php

namespace Spike\Command;

use Spike\Protocol\Message;

abstract class AbstractCommand implements CommandInterface
{
    /**
     * @var array
     */
    protected $payload = [];

    public function __construct(array $payload = [])
    {
        $this->payload = ['_cid_' => $this->getCommandId()] + $payload;
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(): Message
    {
        return new Message(Message::PAYLOAD_CONTROL, $this->payload);
    }

    /**
     * Gets the command id.
     *
     * @return string
     */
    abstract public function getCommandId(): string;
}