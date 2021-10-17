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

namespace Spike\Client\Handler;

use Spike\Command\CommandInterface;
use Spike\Connection\ConnectionInterface;
use Spike\Server\Command\REGISTERBACK;

class RegisterBackHandler extends ClientCommandHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(CommandInterface $command, ConnectionInterface $connection)
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function getSubscribedCommands(): array
    {
        return [REGISTERBACK::class];
    }
}