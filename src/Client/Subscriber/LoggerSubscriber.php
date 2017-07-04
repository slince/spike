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

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            EventStore::CLIENT_RUN => 'onClientRun',
            EventStore::CONNECT_TO_SERVER => 'onConnectToServer',
            EventStore::CANNOT_CONNECT_TO_SERVER => 'onCannotConnectToServer',
            EventStore::RECEIVE_MESSAGE => 'onReceiveMessage',
            EventStore::CONNECTION_ERROR => 'onConnectionError',
            EventStore::AUTH_ERROR => 'onAuthError',
            EventStore::REGISTER_TUNNEL_ERROR => 'onRegisterTunnelError',
            EventStore::DISCONNECT_FROM_SERVER => 'onDisconnectFromServer'
        ];
    }

    public function onAuthError(Event $event)
    {
        $this->logger->error('Auth error, please checks your configuration file');
    }

    public function onRegisterTunnelError(Event $event)
    {
        $this->logger->error(sprintf('Registers the tunnel "%s" error, message: %s',
            $event->getArgument('tunnel'),
            $event->getArgument('errorMessage')
        ));
    }

    public function onDisconnectFromServer(Event $event)
    {
        $this->logger->error('Disconnect from the server');
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
        $this->logger->error("Cannot connect to the server. the server may not be available");
    }
}