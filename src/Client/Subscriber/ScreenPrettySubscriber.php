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
            EventStore::SOCKET_ERROR => 'onClientError',
        ];
    }


    public function onClientRun(Event $event)
    {
        $this->output->writeln("<info>The server is running ...</info>");
    }

    public function onAcceptConnection(Event $event)
    {
        $this->output->writeln("<info>Accepted a new connection.</info>");
    }

    public function onReceiveMessage(Event $event)
    {
        $this->output->writeln("<info>Received a message.</info>");
    }

    public function onClientError(Event $event)
    {
        $this->output->writeln("<warnning>Client error.</warnning>");
    }
}