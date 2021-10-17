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

use Spike\Client\Command\REGISTER;
use Spike\Command\CommandInterface;
use Spike\Command\ERROR;
use Spike\Connection\ConnectionInterface;
use Spike\Server\Client;
use Spike\Server\ClientRegistry;
use Spike\Server\Command\REGISTERBACK;
use Spike\Server\Command\REQUESTPROXY;
use Spike\Server\Configuration;
use Spike\Server\Server;
use Spike\Server\Tunnel;
use Spike\Server\TunnelListener;
use Spike\Server\TunnelListenerCollection;

class RegisterHandler extends ServerCommandHandler
{
    /**
     * @var Configuration
     */
    protected $configuration;

    public function __construct(Server $server, ClientRegistry $clients, Configuration $configuration)
    {
        parent::__construct($server, $clients);
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(CommandInterface $command, ConnectionInterface $connection)
    {
        $client = $this->clients->search($connection);

        if ($this->authenticate($command->getUsername(), $command->getPassword())) {
            $response = new REGISTERBACK(REGISTERBACK::STATUS_OK, $client->getId());
            $connection->executeCommand($response);
            $listeners = $this->createTunnelListeners($client, $command);
            $client->setTunnelListeners($listeners);
            $this->runTunnelListeners($connection, $listeners);
        } else {
            $response = new REGISTERBACK(REGISTERBACK::STATUS_FAIL);
            $connection->executeCommand($response);
            $this->clients->remove($client);
        }
    }

    /**
     * @param ConnectionInterface $connection
     * @param TunnelListenerCollection $listeners
     */
    protected function runTunnelListeners(ConnectionInterface $connection, TunnelListenerCollection $listeners)
    {
        foreach ($listeners as $port => $listener) {
            try {
                $listener->listen();
                $connection->executeCommand(new REQUESTPROXY($port));
            } catch (\Exception $exception) {
                $connection->executeCommand(new ERROR(
                    sprintf('Cannot create tunnel listener for port "%s"; error: %s',
                    $port, $exception->getMessage())
                ));
            }
        }
    }

    protected function createTunnelListeners(Client $client, REGISTER $command): TunnelListenerCollection
    {
        $tunnels = $this->discoverTunnels($command);
        $listeners = [];
        foreach ($tunnels as $tunnel) {
            $listener = new TunnelListener($client, $tunnel);
            $listeners[$tunnel->getPort()]  = $listener;
        }
        return new TunnelListenerCollection($listeners);
    }

    /**
     * Discover tunnels from command arguments.
     *
     * @param REGISTER $command
     * @return Tunnel[]
     */
    protected function discoverTunnels(REGISTER $command): array
    {
        $tunnels = [];
        foreach ($command->getTunnels() as $detail) {
            $tunnels[] = new Tunnel($detail['scheme'], $detail['port']);
        }
        return $tunnels;
    }

    protected function authenticate(string $username, string $password): bool
    {
        foreach ($this->configuration->getUsers() as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSubscribedCommands(): array
    {
        return [REGISTER::class];
    }
}