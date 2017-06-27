<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Subscriber;

use Slince\Event\Event;
use Spike\Client\Application;
use Spike\Client\EventStore;
use Spike\Logger\Logger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class LoggerSubscriber extends Subscriber
{
    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(Application $client)
    {
        parent::__construct($client);
        $this->logger =  $client->getLogger();
    }


    public function getEvents()
    {
        return [
            EventStore::CLIENT_RUN => 'onClientRun',
            EventStore::RECEIVE_MESSAGE => 'onReceiveMessage',
            EventStore::SOCKET_ERROR => 'onSocketError',
            EventStore::CONNECTION_ERROR => 'onConnectionError',
            EventStore::CONNECT_TO_SERVER => 'onConnectToServer',
        ];
    }

    public function onReceiveMessage(Event $event)
    {
        $this->logger->info("Received a message:\r\n" . $event->getArgument('message'));
    }

    public function onClientRun(Event $event)
    {
        $this->logger->info("The client is running ...");
    }

    public function onSocketError(Event $event)
    {
        $this->logger->error("Client error.");
    }

    public function onConnectionError(Event $event)
    {
        $this->logger->warning($event->getArgument('exception')->getMessage());
    }

    public function onConnectToServer(Event $event)
    {
        $this->logger->info("The client has connected to the server.");
    }
}