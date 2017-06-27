<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client;

use Slince\Event\Event;
use Spike\Application as BaseApplication;
use Slince\Event\SubscriberInterface;
use Spike\Client\Command\InitCommand;
use Spike\Client\Command\SpikeCommand;
use Spike\Client\Command\ShowProxyHostsCommand;
use Spike\Client\Subscriber\LoggerSubscriber;
use Spike\Logger\Logger;
use Symfony\Component\Console\Input\InputDefinition;
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
            $this->loop,
            $this->dispatcher
        );
    }

    /**
     * Sets the client for the application
     * @param Client $client
     */
    public function setKernel(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Gets the client instance
     * @return Client
     */
    public function getKernel()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
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
            $exitCode = $this->doRunClient();
        }
        return $exitCode;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function doRunClient()
    {
        if (true === $this->input->hasParameterOption(array('--help', '-h'), true)) {
            $command = $this->get('spike');
            $exitCode = $this->doRunCommand($command, $this->input, $this->output);
        } else {
            $exitCode = $this->runClient();
        }
        return $exitCode;
    }

    /**
     * Start the client
     * @codeCoverageIgnore
     */
    protected function runClient()
    {
        foreach ($this->getSubscribers() as $subscriber) {
            $this->dispatcher->addSubscriber($subscriber);
        }
        $this->client->run();
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            EventStore::AUTH_ERROR => 'onAuthError',
            EventStore::REGISTER_TUNNEL_ERROR => 'onRegisterTunnelError',
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function onAuthError(Event $event)
    {
        $this->output->writeln('Auth error, please checks your config file');
        $this->client->close();
    }

    /**
     * @codeCoverageIgnore
     */
    public function onRegisterTunnelError(Event $event)
    {
        $this->output->writeln(sprintf('Registers the tunnel "%s" error, message: %s',
            $event->getArgument('tunnel'),
            $event->getArgument('errorMessage')
        ));
        $this->client->close();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new SpikeCommand($this),
            new ShowProxyHostsCommand($this),
            new InitCommand($this),
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

    /**
     * {@inheritdoc}
     */
    protected function getDefaultInputDefinition()
    {
        $definition = new InputDefinition([
            new InputOption('config', null, InputOption::VALUE_OPTIONAL,
                'The configuration file, support json,ini,xml and yaml format')
        ]);
        $defaultDefinition = parent::getDefaultInputDefinition();
        $definition->addArguments($defaultDefinition->getArguments());
        $definition->addOptions($defaultDefinition->getOptions());
        return $definition;
    }
}