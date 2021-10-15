<?php

declare(strict_types=1);

namespace Spike\Handler;

use Spike\Command\CommandInterface;
use Spike\Connection\ConnectionInterface;
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\Message;

final class DelegatingHandler implements HandlerInterface
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
    public function handle(CommandInterface $command, ConnectionInterface $connection)
    {
        if (false === $loader = $this->resolver->resolve($command)) {
            throw new InvalidArgumentException(sprintf('Cannot find handler for command type: "%s"',
                get_class($command)
            ));
        }

        return $loader->handle($command, $connection);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(CommandInterface $command): bool
    {
        return false !== $this->resolve($command);
    }
}