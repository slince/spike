<?php


namespace Spike\Server;

use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use Spike\Handler\DelegatingHandler;
use Spike\Handler\MessageHandlerInterface;
use Spike\Protocol\Message;
use Spike\Protocol\MessageParser;
use Spike\Server\Handler\LoginHandler;
use Spike\Server\Handler\PingHandler;
use Spike\Server\Handler\RegisterProxyAwareHandler;
use Spike\Server\Handler\RegisterTunnelAwareHandler;
use Spike\TcpServer;

final class Server extends TcpServer
{
    /**
     * @var ConnectionInterface[]
     */
    protected $clients;

    /**
     * @var MessageParser
     */
    protected $parser;

    /**
     * @var MessageHandlerInterface
     */
    protected $messageHandler;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(Configuration $configuration, LoggerInterface $logger, ?LoopInterface $loop = null)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
        parent::__construct($loop);
    }

    public function addClient(Client $client)
    {
        $this->clients[$client->getId()] =  $client;
    }

    public function getClientById(string $id)
    {
        return $this->clients[$id] ?? null;
    }

    protected function initialize()
    {
        $this->messageHandler = $this->createMessageHandler();
        $this->parser = new MessageParser();
        $this->parser->on('message', function(Message $message, $connection){
            $this->messageHandler->handle($message, $connection);
        });
    }

    protected function createMessageHandler()
    {
        return new DelegatingHandler([
            new LoginHandler($this, $this->configuration),
            new PingHandler($this),
            new RegisterTunnelAwareHandler($this),
            new RegisterProxyAwareHandler($this),
        ]);
    }

    /**
     * @internal
     * @param ConnectionInterface $connection
     */
    public function handleConnection(ConnectionInterface $connection)
    {
        $this->clients[] = $connection;
        $this->parser->handle($connection);
    }
}