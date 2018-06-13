<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Client\Handler;

use Slince\Event\Event;
use Spike\Client\Event\Events;
use Spike\Common\Exception\InvalidArgumentException;
use Spike\Common\Protocol\SpikeInterface;
use Spike\Common\Tunnel\TunnelInterface;

class RegisterTunnelResponseHandler extends MessageActionHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        $tunnelInfo = $message->getBody();

        $tunnel = $this->client->getConfiguration()->getTunnels()->filter(function(TunnelInterface $tunnel) use ($tunnelInfo){
            return $tunnel->match($tunnelInfo);
        })->first();
        if (!$tunnel) {
            throw new InvalidArgumentException('Can not find the matching tunnel');
        }
        if ($message->getHeader('Code') == 0) {
            $event = new Event(Events::REGISTER_TUNNEL_SUCCESS, $this->client, [
                'tunnel' => $tunnel
            ]);
        } else {
            $event = new Event(Events::REGISTER_TUNNEL_ERROR, $this->client, [
                'tunnel' => $tunnel,
                'errorMessage' => $response['error']
            ]);
        }
        $this->getEventDispatcher()->dispatch($event);
    }

    /**
     * Finds the matching tunnel
     * @param array $tunnelInfo
     * @return false|TunnelInterface
     */
    protected function findByInfo($tunnelInfo)
    {
        return $this->client->getConfiguration()->getTunnels()->filter(function(TunnelInterface $tunnel) use ($tunnelInfo){
            return $tunnel->match($tunnelInfo);
        })->first();
    }
}