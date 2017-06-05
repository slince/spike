<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Subscriber;

use Slince\Event\Event;
use Spike\Client;
use Spike\Client\EventStore;
use Spike\Logger\Logger;
use Symfony\Component\Console\Output\OutputInterface;

class LoggerSubscriber extends Subscriber
{
    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(Client $client)
    {
        parent::__construct($client);
        $this->logger =  $client->getLogger();
    }


    public function getEvents()
    {
        return [
            EventStore::CLIENT_RUN => 'onClientRun',
            EventStore::ACCEPT_CONNECTION => 'onAcceptConnection',
            EventStore::SOCKET_ERROR => 'onSocketError',
            EventStore::CONNECT_TO_SERVER => 'onConnectToServer',
            EventStore::RECEIVE_PROXY_REQUEST => 'onReceiveProxyRequest',
            EventStore::SEND_PROXY_RESPONSE => 'onSendProxyResponse',
        ];
    }


    public function onClientRun(Event $event)
    {
        $this->logger->info("The client is running ...");
    }

    public function onAcceptConnection(Event $event)
    {
        $this->logger->info("Accepted a new connection.");
    }

    public function onReceiveMessage(Event $event)
    {
        $this->logger->info("Received a message.");
    }

    public function onSocketError(Event $event)
    {
        $this->logger->error("Client error.");
    }

    public function onConnectToServer(Event $event)
    {
        $this->logger->info("The client has connected to the server.");
    }

    public function onReceiveProxyRequest()
    {
        $this->logger->info("The client receive a proxy request.");
    }

    public function onSendProxyResponse(Event $event)
    {
        $this->logger->info("he client sends a proxy response.");
    }
}