<?php

namespace Spike\Handler;

use Spike\Command\CommandInterface;

final class HandlerResolver
{
    /**
     * @var CommandHandlerInterface[]
     */
    private $handlers;

    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * Returns a handler able to handle the command.
     *
     * @param CommandInterface $command
     * @return bool
     */
    public function resolve(CommandInterface $command)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($command)) {
                return $handler;
            }
        }

        return false;
    }

    /**
     * Add a command handler.
     *
     * @param CommandHandlerInterface $handler
     */
    public function addHandler(CommandHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }
}