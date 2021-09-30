<?php


namespace Spike\Server;

use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use Spike\Connection\ConnectionFactory;
use Spike\Handler\DelegatingHandler;
use Spike\Handler\HandlerResolver;
use Spike\Handler\MessageHandlerInterface;
use Spike\Protocol\Message;
use Spike\Protocol\MessageParser;
use Spike\Server\Handler\LoginHandler;
use Spike\Server\Handler\PingHandler;
use Spike\Server\Handler\RegisterProxyAwareHandler;
use Spike\Server\Handler\RegisterTunnelAwareHandler;
use Spike\Socket\TcpServer;

final class Server extends TcpServer
{
    /**
     * @var MessageParser
     */
    protected $parser;

    /**
     * @var MessageHandlerInterface
     */
    protected $handler;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ClientRegistry
     */
    protected $clients;

    public function __construct(Configuration $configuration, LoggerInterface $logger, ?LoopInterface $loop = null)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
        parent::__construct($loop);
    }

    protected function createMessageHandler(): MessageHandlerInterface
    {
        return new DelegatingHandler(new HandlerResolver([
            new LoginHandler($this, $this->configuration),
            new PingHandler($this),
            new RegisterTunnelAwareHandler($this),
            new RegisterProxyAwareHandler($this),
        ]));
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->clients = new ClientRegistry();
        $this->handler = $this->createMessageHandler();
        $this->parser = new MessageParser();
        $this->parser->on('message', function(Message $message, $connection){
            $this->handler->handle($message, $connection);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function handleConnection(ConnectionInterface $connection)
    {
        $connection = ConnectionFactory::createConnection($connection);
        $this->clients->add(new Client($connection));
        $this->parser->handle($connection);
    }
}