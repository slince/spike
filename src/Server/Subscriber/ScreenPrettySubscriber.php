<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Subscriber;

use Slince\Event\Event;
use Spike\Server;
use Spike\Server\EventStore;
use Symfony\Component\Console\Output\OutputInterface;

class ScreenPrettySubscriber extends Subscriber
{
    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(Server $server)
    {
        parent::__construct($server);
        $this->output =  $server->getOutput();
    }

    public function getEvents()
    {
        return [
            EventStore::SERVER_RUN => 'onServerRun',
            EventStore::ACCEPT_CONNECTION => 'onAcceptConnection',
            EventStore::SOCKET_ERROR => 'onServerError',
            EventStore::SEND_PROXY_REQUEST => 'onSendProxyRequest',
            EventStore::RECEIVE_PROXY_RESPONSE => 'onReceiveProxyResponse',
        ];
    }

    public function onServerRun(Event $event)
    {
        $this->output->writeln("<info>The server is running ...</info>");
    }

    public function onAcceptConnection(Event $event)
    {
        $this->output->writeln("<info>Accepted a new connection.</info>");
    }

    public function onReceiveMessage(Event $event)
    {
    }

    public function onServerError(Event $event)
    {
    }

    public function onSendProxyRequest(Event $event)
    {
        $this->output->writeln(sprintf('<info>Received a proxy request to "%s".</info>',
            $event->getArgument('proxyHost')->getHost()
        ));
    }

    public function onReceiveProxyResponse(Event $event)
    {
        $this->output->writeln(sprintf('<info>Received a proxy response, and has resent it.</info>'));
    }
}