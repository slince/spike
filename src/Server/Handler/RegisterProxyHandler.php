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

namespace Spike\Server\Handler;

use Spike\Command\CommandInterface;
use Spike\Connection\ConnectionInterface;

class RegisterProxyHandler extends ServerCommandHandler
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
        return ['REGISTERPROXY'];
    }
}