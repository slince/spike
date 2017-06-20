<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\SpikeInterface;
use Spike\Protocol\Spike;
use Spike\Server\Client;

class AuthHandler extends MessageHandler
{
    public function handle(SpikeInterface $message)
    {
        $client = new Client($message->getBody(), $this->connection);
        $this->server->getClients()->add($client);
        $this->connection->write(new Spike('auth_response', $client->toArray()));
    }
}