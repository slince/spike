<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Handler;

use React\Socket\ConnectionInterface;
use Spike\Protocol\Message;

class RegisterProxyAwareHandler extends AuthAwareHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(Message $message, ConnectionInterface $connection)
    {
        parent::handle($message, $connection);
        //Fires 'register_proxy' event
        $chunkServer = $this->server->getChunkServers()->findByTunnelInfo($message->getBody());
        if (!$chunkServer) {
            throw new BadRequestException('Can not find the chunk server');
        }
        $connection->removeAllListeners();
        $chunkServer->setProxyConnection($message->getHeader('public-connection-id'), $connection);
    }

    /**
     * @inheritDoc
     */
    public function supports(Message $message)
    {
        return 'register_proxy' === $message->getAction();
    }
}