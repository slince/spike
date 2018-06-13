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

use Spike\Client\Event\Events;
use Spike\Common\Protocol\Spike;
use Spike\Common\Protocol\SpikeInterface;
use Slince\Event\Event;

class AuthResponseHandler extends MessageActionHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        if ($message->getHeader('code') !== 0) {
            $this->getEventDispatcher()->dispatch(new Event(Events::AUTH_ERROR, $this->client, [
                'message' => $message
            ]));
        } else {
            $this->getEventDispatcher()->dispatch(new Event(Events::AUTH_SUCCESS, $this->client, [
                'message' => $message
            ]));
            $clientInfo = $message->getBody();
            $this->client->setId($clientInfo['id']);
            $this->registerTunnels();
        }
    }

    /**
     * Reports the proxy hosts to the server
     */
    protected function registerTunnels()
    {
        $tunnels = $this->client->getConfiguration()->getTunnels();
        $this->getEventDispatcher()->dispatch(new Event(Events::REGISTER_TUNNELS, $this, [
            'tunnels' => $tunnels
        ]));
        foreach ($tunnels as $tunnel) {
            $this->connection->write(new Spike('register_tunnel', $tunnel));
        }
    }
}