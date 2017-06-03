<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use Slince\Event\SubscriberInterface;
use Spike\Client\Command\ShowProxyHostsCommand;
use Spike\Client\Subscriber\ScreenPrettySubscriber;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Client extends Application implements SubscriberInterface
{
    const NAME = 'spike-client';

    const VERSION = '1.0.0.dev';

    /**
     * The client instance
     * @var Client\Client
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
        $this->client = new Client\Client(
            $this->configuration->getServerAddress(),
            null,
            null,
            $this->dispatcher
        );
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
        $this->prepareProxyHosts();
        $this->client->run();
    }

    protected function prepareProxyHosts()
    {
        $proxyHosts = $this->configuration->get('proxy-hosts') ?: [];
        foreach ($proxyHosts  as $proxyHost => $forwardHost) {
            $this->client->addForwardHost($proxyHost, $forwardHost);
        }
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
            new ScreenPrettySubscriber($this)
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