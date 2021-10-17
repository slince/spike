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

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Handler;

use Spike\Client\Command\PING;
use Spike\Command\CommandInterface;
use Spike\Connection\ConnectionInterface;

class PingHandler extends ServerCommandHandler
{
    use AuthenticationAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function handle(CommandInterface $command, ConnectionInterface $connection)
    {
        $this->ensureClientConnectionValid($connection);
        $client = $this->clients->search($connection);
        $client->refresh();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSubscribedCommands(): array
    {
        return [PING::class];
    }
}