<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use Slince\Event\Event;
use Spike\Client\EventStore;
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\SpikeInterface;

class RegisterTunnelResponseHandler extends MessageHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        $response = $message->getBody();
        $tunnel = $this->client->getTunnels()->findByInfo($response);
        if (!$tunnel) {
            throw new InvalidArgumentException('Can not find the matching tunnel');
        }
        if ($message->getHeader('Code') == 0) {
            $event = new Event(EventStore::REGISTER_TUNNEL_SUCCESS, $this->client, [
                'tunnel' => $tunnel
            ]);
        } else {
            $event = new Event(EventStore::REGISTER_TUNNEL_ERROR, $this->client, [
                'tunnel' => $tunnel,
                'errorMessage' => $response['error']
            ]);
        }
        $this->getDispatcher()->dispatch($event);
    }
}