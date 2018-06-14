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

use Slince\Event\Event;
use Slince\Event\SubscriberInterface;
use Spike\Common\Logger\Logger;
use Spike\Server\Event\Events;
use Spike\Server\Event\FilterActionHandlerEvent;
use Spike\Server\Server;

class LoggerListener implements SubscriberInterface
{
    /**
     * @var Server
     */
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->server->getLogger();
    }

    public function getEvents()
    {
        return [
            Events::SERVER_RUN => 'onServerRun',
            Events::ACCEPT_CONNECTION => 'onAcceptConnection',
            Events::SERVER_ACTION => 'onReceiveMessage',
            Events::SOCKET_ERROR => 'onSocketError',
            Events::CONNECTION_ERROR => 'onConnectionError',
        ];
    }

    public function onServerRun(Event $event)
    {
        $this->getLogger()->info('The server is running ...');
    }

    public function onAcceptConnection(Event $event)
    {
        $this->getLogger()->info('Accepted a connection.');
    }

    public function onReceiveMessage(FilterActionHandlerEvent $event)
    {
        $this->getLogger()->info("Received a message:\r\n" . $event->getMessage());
    }

    public function onSocketError(Event $event)
    {
        $this->getLogger()->warning('Received a error: '
            . $event->getArgument('exception')->getMessage());
    }

    public function onConnectionError(Event $event)
    {
        $this->getLogger()->warning(sprintf('Got a bad protocol message: "%s" from "%s"',
            $event->getArgument('exception')->getMessage(),
            $event->getArgument('connection')->getRemoteAddress()
        ));
    }
}