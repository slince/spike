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

use Spike\Client\Command\REGISTERPROXY;
use Spike\Command\CommandInterface;
use Spike\Connection\ConnectionInterface;
use Spike\Server\Connection\ProxyConnection;

class RegisterProxyHandler extends ServerCommandHandler
{
    use AuthenticationAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function handle(CommandInterface $command, ConnectionInterface $connection)
    {
        $clientId = $command->getClientId();
        $this->ensureClientValid($clientId);
        $client = $this->clients->get($clientId);
        $listeners = $client->getTunnelListeners();
        $listener = $listeners->get($command->getServerPort());
        $listener->getProxyConnections()->add(new ProxyConnection($connection));
        $listener->consumePublicConnections();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSubscribedCommands(): array
    {
        return [REGISTERPROXY::class];
    }
}