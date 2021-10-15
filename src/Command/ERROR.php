<?php

namespace Spike\Command;

use Spike\Protocol\Message;

class ERROR implements CommandInterface
{
    /**
     * @var string
     */
    protected $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandId(): string
    {
        return 'ERROR';
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(): Message
    {
        return new Message(Message::PAYLOAD_ERROR, ['message' => $this->message]);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static($message->getArgument('message'));
    }
}