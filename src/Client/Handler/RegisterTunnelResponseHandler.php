<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use Slince\Event\Event;
use Spike\Client\EventStore;
use Spike\Client\Tunnel\TunnelFactory;
use Spike\Protocol\MessageInterface;

class RegisterTunnelResponseHandler extends Handler
{
    public function handle(MessageInterface $message)
    {
        $response = $message->getBody();
        $tunnel = TunnelFactory::fromArray($response['tunnel']);
        if ($message->getCode() == 0) {
            $event = new Event(EventStore::REGISTER_TUNNEL_SUCCESS, $this->client, [
                'tunnelInfo' => $tunnel
            ]);
        } else {
            $event = new Event(EventStore::REGISTER_TUNNEL_ERROR, $this->client, [
                'tunnelInfo' => $tunnel,
                'errorMessage' => $response['error']
            ]);
        }
        $this->getDispatcher()->dispatch($event);
    }
}