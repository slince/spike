<?php


namespace Spike\Server;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use Spike\Io\Message;
use Spike\Io\MessageParser;
use Spike\Server\Handler\DelegatingHandler;
use Spike\Server\Handler\LoginHandler;
use Spike\Server\Handler\MessageHandlerInterface;
use Spike\Server\Handler\PingHandler;
use Spike\Server\Handler\RegisterProxyHandler;
use Spike\Server\Handler\RegisterTunnelHandler;
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
        $this->clients[] =  $client;
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
            new RegisterTunnelHandler($this),
            new RegisterProxyHandler($this),
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