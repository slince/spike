<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\MessageInterface;
use Spike\Protocol\Spike;
use Spike\Server\Client;

class AuthHandler extends MessageHandler
{
    public function handle(MessageInterface $message)
    {
        $client = new Client($message->getBody(), $this->connection);
        $this->server->addClient($client);
        $this->connection->write(new Spike('auth_response', $client->toArray()));
    }
}