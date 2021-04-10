<?php


namespace Spike\Server;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use Spike\Io\Message;
use Spike\Io\MessageParser;
use Spike\Server\Handler\DelegatingHandler;
use Spike\Server\Handler\LoginHandler;
use Spike\Server\Handler\MessageHandlerInterface;
use Spike\Server\Handler\PingAwareHandler;
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

    public function __construct(Configuration $configuration, ?LoopInterface $loop = null)
    {
        $this->configuration = $configuration;
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
            new PingAwareHandler($this),
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