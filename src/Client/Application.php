<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client;

use Spike\Application as BaseApplication;
use Slince\Event\SubscriberInterface;
use Spike\Client\Command\ShowProxyHostsCommand;
use Spike\Client\Subscriber\LoggerSubscriber;
use Spike\Logger\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication implements SubscriberInterface
{
    /**
     * @var string
     */
    const NAME = 'spike-client';

    /**
     * @var string
     */
    const VERSION = '1.0.0.dev';

    /**
     * The client instance
     * @var Client
     */
    protected $client;

    /**
     * The server address
     * @var string
     */
    protected $serverAddress;

    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration,static::NAME, static::VERSION);
        $this->client = new Client(
            $configuration->getServerAddress(),
            $configuration->getTunnels(),
            $configuration->get('auth') ?: [],
            null,
            $this->dispatcher
        );
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        //Logger
        $this->logger = new Logger(
            $this->getConfiguration()->getLogLevel(),
            $this->getConfiguration()->getLogFile(),
            $this->output
        );
        $this->client->setLogger($this->logger);
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
//            EventStore::CONNECTION_ERROR => 'onConnectionError'
        ];
    }

    /**
     * Start the server
     */
    protected function doRunServer()
    {
        foreach ($this->getSubscribers() as $subscriber) {
            $this->dispatcher->addSubscriber($subscriber);
        }
        $this->client->run();
    }

    public function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new ShowProxyHostsCommand($this),
        ]);
    }

    /**
     * Gets all subscribers
     * @return array
     */
    public function getSubscribers()
    {
        return [
            $this,
            new LoggerSubscriber($this)
        ];
    }

    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption('config', null, InputOption::VALUE_OPTIONAL,
            'The configuration file, support json,ini,xml and yaml format'));

        $definition->addOption(new InputOption('address', null, InputOption::VALUE_OPTIONAL,
            'The server address'));
        return $definition;
    }
}