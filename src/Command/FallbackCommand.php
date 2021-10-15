<?php

declare(strict_types=1);

namespace Spike\Command;

use Spike\Protocol\Message;

class FallbackCommand implements CommandInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $arguments = [];

    public function __construct(string $id, array $arguments = [])
    {
        $this->id = $id;
        $this->arguments = $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandId(): string
    {
        return $this->id;
    }

    /**
     * Returns the command arguments.
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Return the given argument.
     *
     * @param string $name
     * @return mixed
     */
    public function getArgument(string $name)
    {
        return $this->arguments[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(): Message
    {
        return new Message(Message::PAYLOAD_CONTROL, $this->arguments);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static($message->getArgument('_cid_'), $message->getPayload());
    }
}