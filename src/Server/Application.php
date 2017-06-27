<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use Spike\Application as BaseApplication;
use Slince\Event\SubscriberInterface;
use Spike\Server\Command\InitCommand;
use Spike\Server\Command\SpikeCommand;
use Spike\Server\Subscriber\LoggerSubscriber;
use Spike\Logger\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication implements SubscriberInterface
{
    /**
     * @var string
     */
    const NAME = 'spike-server';

    /**
     * @var string
     */
    const VERSION = '1.0.0.dev';

    /**
     * @var Server
     */
    protected $server;

    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration,static::NAME, static::VERSION);
        $this->server = new Server(
            $configuration->getAddress(),
            $configuration->getAuthentication(),
            $this->loop,
            $this->dispatcher
        );
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
        $this->server->setLogger($this->logger);
        $commandName = $input->getFirstArgument();
        if ($commandName) {
            $exitCode = parent::doRun($input, $output);
        } else {
            $exitCode = $this->doRunServer();
        }
        return $exitCode;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function doRunServer()
    {
        if (true === $this->input->hasParameterOption(array('--help', '-h'), true)) {
            $command = $this->get('spiked');
            $exitCode = $this->doRunCommand($command, $this->input, $this->output);
        } else {
            $exitCode = $this->runServer();
        }
        return $exitCode;
    }

    /**
     * Start the server
     * @codeCoverageIgnore
     */
    protected function runServer()
    {
        foreach ($this->getSubscribers() as $subscriber) {
            $this->dispatcher->addSubscriber($subscriber);
        }
        $this->server->run();
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
        ];
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
    public function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new SpikeCommand($this),
            new InitCommand($this)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption(new InputOption('config', null, InputOption::VALUE_REQUIRED,
            'The configuration file, support json,ini,xml and yaml format'));
        $definition->addOption(new InputOption('address', null, InputOption::VALUE_REQUIRED,
            'The ip address that bind to'));
        return $definition;
    }
}