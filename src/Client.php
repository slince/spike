<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use Slince\Event\Event;
use Slince\Event\SubscriberInterface;
use Spike\Client\EventStore;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Client extends Application implements SubscriberInterface
{
    const NAME = 'spike-client';

    const VERSION = '1.0.0.dev';

    /**
     * @var Client\Client
     */
    protected $client;

    public function __construct($configFile = null)
    {
        parent::__construct(static::NAME, static::VERSION);
        is_null($configFile) || $this->configuration->load($configFile);
        $this->client = new Client\Client($this->configuration->getServerAddress(), null, null, $this->dispatcher);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $commandName = $input->getFirstArgument();
        if ($commandName) {
            $exitCode = parent::doRun($input, $output);
        } else {
            $exitCode = $this->doRunServer();
        }
        return $exitCode;
    }

    public function getEvents()
    {
        return [
            EventStore::CLIENT_RUN => 'onClientRun',
            EventStore::ACCEPT_CONNECTION => 'onAcceptConnection',
            EventStore::SOCKET_ERROR => 'onClientError',
        ];
    }

    /**
     * Start the server
     */
    protected function doRunServer()
    {
        $this->dispatcher->addSubscriber($this);
        $this->client->run();
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