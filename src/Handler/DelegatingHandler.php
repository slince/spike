<?php

namespace Spike\Handler;

use React\Socket\ConnectionInterface;
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\Message;

final class DelegatingHandler implements MessageHandlerInterface
{
    /**
     * @var HandlerResolver
     */
    protected $resolver;

    public function __construct(HandlerResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message, ConnectionInterface $connection)
    {
        if (false === $loader = $this->resolver->resolve($message)) {
            throw new InvalidArgumentException(sprintf('Cannot find handler for message type: "%s"',
                get_class($message)
            ));
        }

        return $loader->handle($message, $connection);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Message $message): bool
    {
        return false !== $this->resolve($message);
    }
}