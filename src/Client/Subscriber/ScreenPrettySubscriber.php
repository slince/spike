<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Subscriber;

use Slince\Event\Event;
use Spike\Client;
use Spike\Client\EventStore;
use Symfony\Component\Console\Output\OutputInterface;

class ScreenPrettySubscriber extends Subscriber
{
    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(Client $client)
    {
        parent::__construct($client);
        $this->output =  $client->getOutput();
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
        $this->output->writeln("<info>The client is running ...</info>");
    }

    public function onAcceptConnection(Event $event)
    {
        $this->output->writeln("<info>Accepted a new connection.</info>");
    }

    public function onReceiveMessage(Event $event)
    {
        $this->output->writeln("<info>Received a message.</info>");
    }

    public function onSocketError(Event $event)
    {
        $this->output->writeln("<error>Client error.</error>");
    }

    public function onConnectToServer(Event $event)
    {
        $this->output->writeln("<info>The client has connected to the server.</info>");
    }

    public function onReceiveProxyRequest()
    {
        $this->output->writeln("<info>The client receive a proxy request.</info>");
    }

    public function onSendProxyResponse(Event $event)
    {
        $this->output->writeln("<info>The client sends a proxy response.</info>");
    }
}