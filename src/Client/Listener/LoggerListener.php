<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Client\Listener;

use Slince\Event\Event;
use Slince\Event\SubscriberInterface;
use Spike\Client\Client;
use Spike\Client\Event\Events;
use Spike\Client\Event\FilterActionHandlerEvent;
use Spike\Common\Logger\Logger;

/**
 * @codeCoverageIgnore
 */
class LoggerListener implements SubscriberInterface
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->client->getLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            Events::CLIENT_RUN => 'onClientRun',
            Events::CLIENT_CONNECT => 'onConnectToServer',
            Events::CANNOT_CONNECT_SERVER => 'onCannotConnectToServer',
            Events::CLIENT_ACTION => 'onReceiveMessage',
            Events::CONNECTION_ERROR => 'onConnectionError',
            Events::AUTH_ERROR => 'onAuthError',
            Events::AUTH_SUCCESS => 'onAuthSuccess',
            Events::REGISTER_TUNNEL_ERROR => 'onRegisterTunnelError',
            Events::DISCONNECT_FROM_SERVER => 'onDisconnectFromServer',
        ];
    }

    public function onAuthError(Event $event)
    {
        $this->getLogger()->error('Auth error, please check your configuration file');
    }

    public function onAuthSuccess(Event $event)
    {
        $this->getLogger()->info('Auth success');
    }

    public function onRegisterTunnelError(Event $event)
    {
        $this->getLogger()->error(sprintf('Registers tunnel "%s" error, message: "%s"',
            $event->getArgument('tunnel'),
            $event->getArgument('errorMessage')
        ));
    }

    public function onDisconnectFromServer(Event $event)
    {
        $this->getLogger()->error('Disconnect from the server');
    }

    public function onReceiveMessage(FilterActionHandlerEvent $event)
    {
        $this->getLogger()->info("Received a message:\r\n".$event->getMessage());
    }

    public function onClientRun(Event $event)
    {
        $this->getLogger()->info('The client is running ...');
    }

    public function onConnectionError(Event $event)
    {
        $this->getLogger()->warning(sprintf('Got a bad protocol message: "%s" from "%s"',
            $event->getArgument('exception')->getMessage(),
            $event->getArgument('connection')->getRemoteAddress()
        ));
    }

    public function onConnectToServer(Event $event)
    {
        $this->getLogger()->info('The client has connected to the server.');
    }

    public function onCannotConnectToServer(Event $event)
    {
        $this->getLogger()->error('Cannot connect to the server. the server may not be available');
    }
}