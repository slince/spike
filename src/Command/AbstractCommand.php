<?php

namespace Spike\Command;

use Spike\Protocol\Message;

abstract class AbstractCommand implements CommandInterface
{
    /**
     * @var array
     */
    protected $arguments = [];

    public function __construct(array $arguments = [])
    {
        $this->arguments = ['_cid_' => $this->getCommandId()] + $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(): Message
    {
        return new Message(Message::PAYLOAD_CONTROL, $this->arguments);
    }

    /**
     * Gets the command id.
     *
     * @return string
     */
    abstract public function getCommandId(): string;
}