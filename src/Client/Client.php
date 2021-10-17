<?php

namespace Spike\Client;

use Evenement\EventEmitter;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Socket\Connector;
use React\Socket\ConnectionInterface as RawConnection;
use Spike\Client\Command\REGISTER;
use Spike\Command\CommandFactory;
use Spike\Connection\ConnectionFactory;
use Spike\Connection\ConnectionInterface;
use Spike\Handler\DelegatingHandler;
use Spike\Handler\HandlerInterface;
use Spike\Handler\HandlerResolver;
use Spike\Protocol\Message;
use Spike\Protocol\MessageParser;
use Spike\Server\Command as ServerCommand;

final class Client extends EventEmitter
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @var CommandFactory
     */
    protected $commands;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function __construct(Configuration $configuration, ?LoggerInterface $logger = null, ?LoopInterface $loop = null)
    {
        $this->configuration = $configuration;
        $this->logger = $logger ?: new NullLogger();
        $this->loop = $loop ?: Loop::get();
    }

    public function run()
    {
        $this->initialize();
        $this->tryConnect();
    }

    protected function initialize()
    {
        $this->handler = $this->createCommandHandler();
        $this->commands = $this->createCommandFactory();
    }

    protected function tryConnect()
    {
        $connector = new Connector(['timeout' => $this->configuration->getTimeout()]);
        $connector->connect($this->configuration->getServerAddress())
            ->then([$this, 'handleConnection'], function(\Exception $e){
                $this->logger->error(sprintf('Cannot connect to the server %s; error message: %s', $this->configuration->getServerAddress(), $e->getMessage()));
            });
    }

    /**
     * {@internal}
     */
    public function handleConnection(RawConnection $connection)
    {
        $this->logger->info(sprintf('Connect to the server %s', $this->configuration->getServerAddress()));
        $connection = ConnectionFactory::wrapConnection($connection);
        $this->connection = $connection;
        $this->registerClient();
        $connection->on('message', function(Message $message, $connection){
            $this->logger->info(sprintf('Accept a command [%s], connection "%s"',
                $message->getRawPayload(),
                $connection->getRemoteAddress()
            ));
            $command = $this->commands->createCommand($message);
            $this->handler->handle($command, $connection);
        });
        (new MessageParser($connection))->parse();
    }

    /**
     * Send client info to the server.
     */
    protected function registerClient()
    {
        $user = $this->configuration->getUser();
        $tunnels = array_map(function (Tunnel $tunnel) {
            return ['scheme' => $tunnel->getScheme(), 'server_port' => $tunnel->getServerPort()];
        }, $this->configuration->getTunnels());
        $this->connection->executeCommand(new REGISTER(
            $user['username'], $user['password'], $tunnels
        ));
    }

    /**
     * Create command factory for the client.
     *
     * @return CommandFactory
     */
    protected function createCommandFactory(): CommandFactory
    {
        return new CommandFactory([
            'REGISTERBACK' => ServerCommand\REGISTERBACK::class,
            'REQUESTPROXY' => ServerCommand\REQUESTPROXY::class
        ]);
    }

    /**
     * Create default command handler for the server.
     *
     * @return HandlerInterface
     */
    protected function createCommandHandler(): HandlerInterface
    {
        return new DelegatingHandler(new HandlerResolver([
            new Handler\RegisterBackHandler($this),
            new Handler\RequestProxyHandler($this),
        ], $this->logger));
    }
}