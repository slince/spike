<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Server\Handler;

use Slince\Event\Event;
use Spike\Common\Exception\BadRequestException;
use Spike\Common\Protocol\SpikeInterface;
use Spike\Server\Event\Events;

class RegisterProxyHandler extends RequireAuthHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        parent::handle($message);
        //Fires 'register_proxy' event
        $this->getEventDispatcher()->dispatch(new Event(Events::REGISTER_PROXY, $this, [
            'message' => $message
        ]));
        $chunkServer = $this->server->getChunkServers()->findByTunnelInfo($message->getBody());
        if (!$chunkServer) {
            throw new BadRequestException("Can not find the chunk server");
        }
        $this->connection->removeAllListeners();
        $chunkServer->setProxyConnection($message->getHeader('public-connection-id'), $this->connection);
    }
}