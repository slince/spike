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
namespace Spike\Handler\Server;

use Spike\Command\Client\REGISTER;
use Spike\Command\CommandInterface;
use Spike\Command\Server\REGISTERBACK;
use Spike\Connection\ConnectionInterface;
use Spike\Protocol\Message;
use Spike\Server\Client;
use Spike\Server\ClientRegistry;
use Spike\Server\Configuration;
use Spike\Server\Server;

class RegisterHandlerServer extends ServerCommandHandler
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var ClientRegistry
     */
    protected $clients;

    public function __construct(Server $server, Configuration $configuration, ClientRegistry $clients)
    {
        parent::__construct($server);
        $this->configuration = $configuration;
        $this->clients = $clients;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(CommandInterface $command, ConnectionInterface $connection)
    {
        $user = $command->getArguments();
        $client = $this->clients->search($connection);

        if ($this->authenticate($user['username'], $user['password'])) {
            $response = new REGISTERBACK(REGISTERBACK::STATUS_OK, $client->getId());
            $connection->executeCommand($response);
        } else {
            $response = new REGISTERBACK(REGISTERBACK::STATUS_FAIL);
            $connection->executeCommand($response);
            $this->clients->remove($client);
        }
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