<?php

namespace Spike\Client\Command;

use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class PING implements CommandInterface
{
    /**
     * @var \DateTimeInterface
     */
    protected $requestAt;

    public function __construct(?\DateTimeInterface $requestAt = null)
    {
        $this->requestAt = $requestAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandId(): string
    {
        return 'PING';
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(): Message
    {
        return new Message(Message::PAYLOAD_CONTROL, [
            'request_at' => $this->requestAt->format(\DateTimeInterface::ISO8601)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static(new \DateTime($message->getArgument('request_at')));
    }
}