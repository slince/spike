<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Handler\Server;

use Spike\Command\CommandInterface;
use Spike\Handler\HandlerInterface;
use Spike\Server\Server;

abstract class MessageHandler implements HandlerInterface
{
    /**
     * @var Server
     */
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Returns the subscribed command types.
     *
     * @return array
     */
    abstract protected function getSubscribedCommands(): array;

    /**
     * {@inheritdoc}
     */
    public function supports(CommandInterface $command): bool
    {
        return in_array(get_class($command), $this->getSubscribedCommands());
    }
}