<?php


namespace Spike\Server\Handler;

use React\Socket\ConnectionInterface;
use Spike\Protocol\Message;
use Spike\Server\Client;
use Spike\Server\Configuration;
use Spike\Server\Server;

class LoginHandler extends MessageCommandHandler
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
    public function handle(Message $message, ConnectionInterface $connection)
    {
        $user = $message->getPayload();
        if ($this->checkUser($user['username'], $user['password'])) {
            $client = new Client($connection);
            $this->server->addClient($client);
            $response = new Message('login_response', ['id' => $client->getId()]);
        } else {
            $response = new Message('login_response', ['error' => 'Wrong username or password']);
        }
        $connection->write($response);
    }

    protected function checkUser(string $username, string $password)
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
    public function supports(Message $message)
    {
        return 'login' === $message->getAction();
    }
}