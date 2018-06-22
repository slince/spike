<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Listener;

use React\Socket\ConnectionInterface;
use Slince\EventDispatcher\Event;
use Slince\EventDispatcher\SubscriberInterface;
use Spike\Common\Exception\InvalidArgumentException;
use Spike\Common\Protocol\SpikeInterface;
use Spike\Server\Event\Events;
use Spike\Server\Event\FilterActionHandlerEvent;
use Spike\Server\Handler;
use Spike\Server\Server;

class ServerListener implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::SERVER_ACTION => 'onServerAction',
            Events::CONNECTION_ERROR => 'onConnectionError',
        ];
    }

    /**
     * @param Event $event
     */
    public function onConnectionError(Event $event)
    {
        $event->getArgument('connection')->end('bad message');
    }

    /**
     * @param FilterActionHandlerEvent $event
     */
    public function onServerAction(FilterActionHandlerEvent $event)
    {
        $actionHandler = $this->createMessageHandler(
            $event->getSubject(),
            $event->getMessage(),
            $event->getConnection()
        );
        $event->setActionHandler($actionHandler);
    }

    /**
     * Creates the handler for the received message.
     *
     * @param Server              $server
     * @param SpikeInterface      $message
     * @param ConnectionInterface $connection
     *
     * @return Handler\ActionHandlerInterface
     * @codeCoverageIgnore
     */
    protected function createMessageHandler(Server $server, SpikeInterface $message, ConnectionInterface $connection)
    {
        switch ($message->getAction()) {
            case 'auth':
                $handler = new Handler\AuthHandler($server, $connection);
                break;
            case 'ping':
                $handler = new Handler\PingHandler($server, $connection);
                break;
            case 'register_tunnel':
                $handler = new Handler\RegisterTunnelHandler($server, $connection);
                break;
            case 'register_proxy':
                $handler = new Handler\RegisterProxyHandler($server, $connection);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Cannot find handler for message type: "%s"',
                    get_class($message)
                ));
        }

        return $handler;
    }
}