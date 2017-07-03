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
            EventStore::CONNECT_TO_SERVER => 'onConnectToServer',
            EventStore::CANNOT_CONNECT_TO_SERVER => 'onCannotConnectToServer',
            EventStore::RECEIVE_MESSAGE => 'onReceiveMessage',
            EventStore::CONNECTION_ERROR => 'onConnectionError',
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

    public function onConnectionError(Event $event)
    {
        $this->logger->warning(sprintf('Got a bad protocol message: "%s" from "%s"',
            $event->getArgument('exception')->getMessage(),
            $event->getArgument('connection')->getRemoteAddress()
        ));
    }

    public function onConnectToServer(Event $event)
    {
        $this->logger->info("The client has connected to the server.");
    }

    public function onCannotConnectToServer(Event $event)
    {
        $this->logger->info("Cannot connect to the server. the server may not be available");
    }
}