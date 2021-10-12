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

use Spike\Command\CommandInterface;

abstract class CommandHandler implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(CommandInterface $command): bool
    {
        return in_array(get_class($command), $this->getSubscribedCommands());
    }

    /**
     * Returns the subscribed command types.
     *
     * @return array
     */
    abstract protected function getSubscribedCommands(): array;
}