<?php


namespace Spike\Handler\Server;

use Spike\Command\Client\REGISTER;
use Spike\Command\CommandInterface;
use Spike\Connection\ConnectionInterface;
use Spike\Protocol\Message;
use Spike\Server\Client;
use Spike\Server\Configuration;
use Spike\Server\Server;

class LoginHandler extends MessageHandler
{
    /**
     * @var Configuration
     */
    protected $configuration;

    public function __construct(Server $server, Configuration $configuration)
    {
        parent::__construct($server);
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(CommandInterface $command, ConnectionInterface $connection)
    {
        $user = $command->getArguments();
        if ($this->authenticate($user['username'], $user['password'])) {
            $client = new Client($connection);
            $this->server->addClient($client);
            $response = new Message('login_response', ['id' => $client->getId()]);
        } else {
            $response = new Message('login_response', ['error' => 'Wrong username or password']);
        }
        $connection->write($response);
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