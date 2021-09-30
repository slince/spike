<?php

namespace Spike\Handler;

use Spike\Protocol\Message;

final class HandlerResolver
{
    /**
     * @var MessageHandlerInterface[]
     */
    private $handlers;

    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * Returns a handler able to load the resource.
     *
     * @param Message $message
     * @return bool
     */
    public function resolve(Message $message)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($message)) {
                return $handler;
            }
        }

        return false;
    }

    /**
     * Add a message handler.
     *
     * @param MessageHandlerInterface $handler
     */
    public function addHandler(MessageHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }
}