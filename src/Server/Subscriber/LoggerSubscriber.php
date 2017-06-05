<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Subscriber;

use Slince\Event\Event;
use Spike\Logger\Logger;
use Spike\Server;
use Spike\Server\EventStore;

class LoggerSubscriber extends Subscriber
{
    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(Server $server)
    {
        parent::__construct($server);
        $this->logger = $server->getLogger();
    }

    public function getEvents()
    {
        return [
            EventStore::SERVER_RUN => 'onServerRun',
            EventStore::ACCEPT_CONNECTION => 'onAcceptConnection',
            EventStore::SOCKET_ERROR => 'onSocketError',
            EventStore::SEND_PROXY_REQUEST => 'onSendProxyRequest',
            EventStore::RECEIVE_PROXY_RESPONSE => 'onReceiveProxyResponse',
            EventStore::CONNECTION_ERROR => 'onConnectionError',
            EventStore::RECEIVE_CLIENT_EXCEPTION => 'onClientException',
        ];
    }

    public function onServerRun(Event $event)
    {
        $this->logger->info('The server is running ...');
    }

    public function onAcceptConnection(Event $event)
    {
        $this->logger->info('Accepted a new connection.');
    }

    public function onReceiveMessage(Event $event)
    {
        $this->logger->info('Received a new message.');
    }

    public function onSocketError(Event $event)
    {
        $this->logger->warning('Received a error: ' . $event->getArgument('exception'));
    }

    public function onConnectionError(Event $event)
    {
        $this->logger->warning($event->getArgument('exception')->getMessage());
    }

    public function onSendProxyRequest(Event $event)
    {
        $this->logger->info(sprintf('Received a proxy request to "%s".',
            $event->getArgument('proxyHost')->getHost()
        ));
    }

    public function onReceiveProxyResponse(Event $event)
    {
        $this->logger->info(sprintf('Received a proxy response, and has resent it.'));
    }

    public function onClientException(Event $event)
    {
        $this->logger->info(sprintf('Received an exception from the client.'));
    }
}