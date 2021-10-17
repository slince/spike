<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Handler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Spike\Command\CommandInterface;

final class HandlerResolver
{
    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(array $handlers, LoggerInterface $logger)
    {
        $this->logger = $logger;
        foreach ($handlers as $handler) {
            $this->addHandler($handler);
        }
    }

    /**
     * Returns a handler able to handle the command.
     *
     * @param CommandInterface $command
     * @return HandlerInterface|null
     */
    public function resolve(CommandInterface $command): ?HandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($command)) {
                return $handler;
            }
        }

        return null;
    }

    /**
     * Add a command handler.
     *
     * @param HandlerInterface $handler
     */
    public function addHandler(HandlerInterface $handler)
    {
        if ($handler instanceof LoggerAwareInterface) {
            $handler->setLogger($this->logger);
        }
        $this->handlers[] = $handler;
    }
}