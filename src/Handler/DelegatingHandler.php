<?php

namespace Spike\Handler;

use React\Socket\ConnectionInterface;
use Spike\Exception\InvalidArgumentException;
use Spike\Io\Message;

class DelegatingHandler implements MessageHandlerInterface
{
    /**
     * @var MessageHandlerInterface[]
     */
    protected $handlers;

    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @inheritDoc
     */
    public function handle(Message $message, ConnectionInterface $connection)
    {
        if (false === $loader = $this->resolve($message)) {
            throw new InvalidArgumentException(sprintf('Cannot find handler for message type: "%s"',
                get_class($message)
            ));
        }

        return $loader->handle($message, $connection);
    }

    protected function resolve(Message $message)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($message)) {
                return $handler;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function supports(Message $message)
    {
        return false !== $this->resolve($message);
    }
}