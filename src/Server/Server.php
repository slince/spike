<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server;

use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use Spike\Command\CommandFactory;
use Spike\Connection\ConnectionFactory;
use Spike\Handler\DelegatingHandler;
use Spike\Handler\HandlerResolver;
use Spike\Handler\HandlerInterface;
use Spike\Server\Handler as ServerHandler;
use Spike\Protocol\Message;
use Spike\Socket\TcpServer;

final class Server extends TcpServer
{
    /**
     * @var HandlerInterface
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

    /**
     * @var CommandFactory
     */
    protected $commands;

    public function __construct(Configuration $configuration, ?LoggerInterface $logger = null, ?LoopInterface $loop = null)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
        parent::__construct($loop);
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $this->clients = new ClientRegistry();
        $this->handler = $this->createCommandHandler();
        $this->commands = $this->createCommandFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function handleConnection(ConnectionInterface $connection)
    {
        $connection = ConnectionFactory::wrapConnection($connection);
        $this->clients->add(new Client($connection));
        $connection->listen(function(Message $message, $connection){
            $command = $this->commands->createCommand($message);
            $this->handler->handle($command, $connection);
        });
    }

    /**
     * Create command factory for the server.
     *
     * @return CommandFactory
     */
    protected function createCommandFactory(): CommandFactory
    {
        return new CommandFactory([
            'REGISTERBACK' => Command\REGISTERBACK::class
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
            new ServerHandler\RegisterHandlerServer($this, $this->configuration, $this->clients),
            new ServerHandler\PingHandler($this),
            new ServerHandler\RegisterTunnelAwareHandler($this),
            new ServerHandler\RegisterProxyAwareHandler($this),
        ]));
    }
}