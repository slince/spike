<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use Spike\Protocol\MessageInterface;
use Slince\Event\Event;
use Spike\Client\EventStore;
use Spike\Protocol\Spike;

class AuthResponseHandler extends MessageHandler
{
    public function handle(MessageInterface $message)
    {
        $clientInfo = $message->getBody();
        $this->client->setClientId($clientInfo['id']);
        $this->transferTunnels();
    }

    /**
     * Reports the proxy hosts to the server
     */
    protected function transferTunnels()
    {
        $this->client->getDispatcher()->dispatch(new Event(EventStore::REGISTER_TUNNELS, $this, [
            'tunnels' => $this->client->getTunnels()
        ]));
        foreach ($this->client->getTunnels() as $tunnel) {
            $this->connection->write(new Spike('register_tunnel', $tunnel->toArray()));
            break;
        }
    }
}