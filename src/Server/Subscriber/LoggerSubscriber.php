<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Subscriber;

use Slince\Event\Event;
use Spike\Logger\Logger;
use Spike\Server\EventStore;
use Spike\Server\Application;

class LoggerSubscriber extends Subscriber
{
    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(Application $server)
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
            EventStore::CONNECTION_ERROR => 'onConnectionError',
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
        $this->logger->warning('Received a error: '
            . $event->getArgument('exception')->getMessage());
    }

    public function onConnectionError(Event $event)
    {
        $this->logger->warning(sprintf('Got a bad protocol message: "%s" from "%s"',
            $event->getArgument('exception')->getMessage(),
            $event->getArgument('connection')->getRemoteAddress()
        ));
    }
}