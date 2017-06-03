<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use Slince\Event\Event;
use Slince\Event\SubscriberInterface;
use Spike\Server\EventStore;
use Spike\Server\Exception\MissingProxyClientException;
use Spike\Server\Subscriber\LoggerSubscriber;
use Spike\Logger\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Server extends Application implements SubscriberInterface
{
    const NAME = 'spike-server';

    const VERSION = '1.0.0.dev';

    /**
     * @var Server\Server
     */
    protected $server;

    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration,static::NAME, static::VERSION);
        $this->server = new Server\Server($this->configuration->getAddress(), null, $this->dispatcher);
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
        $commandName = $input->getFirstArgument();
        if ($commandName) {
            $exitCode = parent::doRun($input, $output);
        } else {
            $exitCode = $this->doRunServer();
        }
        return $exitCode;
    }

    /**
     * Start the server
     */
    protected function doRunServer()
    {
        foreach ($this->getSubscribers() as $subscriber) {
            $this->dispatcher->addSubscriber($subscriber);
        }
        $this->server->run();
    }

    public function getEvents()
    {
        return [
            EventStore::CONNECTION_ERROR => 'onConnectionError'
        ];
    }

    public function onConnectionError(Event $event)
    {
        $exception = $event->getArgument('exception');
        $connection = $event->getArgument('connection');
        if ($exception instanceof MissingProxyClientException) {
            $response = $this->createErrorResponse();
            $connection->write(Psr7\str($response));
        }
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
            'The ip address that bind to'));
        return $definition;
    }


    protected function createErrorResponse($status = 500, $body = ' ')
    {
        $body = $body ?: 'Did not find the proxy client, or the proxy client did not respond';
        return new Response($status, [
            'Content-Length' => strlen($body),
        ], $body);
    }
}