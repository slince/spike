<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use function Slince\Common\jsonBuffer;
use Slince\Event\Dispatcher;
use Slince\Event\DispatcherInterface;
use Slince\Event\Event;
use Spike\Client\ClientInterface;
use Spike\Common\Protocol\Spike;
use Spike\Server\ChunkServer\ChunkServerCollection;
use Spike\Server\Event\Events;
use Spike\Server\Event\FilterActionHandlerEvent;
use Spike\Server\Listener\ServerListener;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Server extends Application implements ServerInterface
{
    const NAME = 'Spike Server';

    const VERSION = '0.0.1';

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * @var ChunkServerCollection
     */
    protected $chunkServers;

    /**
     * @var Collection
     */
    protected $clients;

    /**
     * @var DispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(Configuration $configuration, LoopInterface $eventLoop = null)
    {
        $this->configuration = $configuration;
        $this->eventLoop = $eventLoop ?: Factory::create();
        $this->eventDispatcher = new Dispatcher();
        $this->chunkServers = new ChunkServerCollection();
        $this->clients = new ArrayCollection();
        $this->initializeEvents();
        parent::__construct(static::NAME, static::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getChunkServers()
    {
        return $this->chunkServers;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->start();
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function start()
    {
        $server = new Socket($this->configuration->getAddress(), $this->eventLoop);
        $this->eventDispatcher->dispatch(Events::SERVER_RUN);
        $server->on('connection', [$this, 'handleControlConnection']);
        $this->eventLoop->run();
    }

    /**
     * Handle control connection
     *
     * @param ConnectionInterface $connection
     */
    public function handleControlConnection(ConnectionInterface $connection)
    {
        jsonBuffer($connection)->then(function($messages, $connection){
            foreach ($messages as $messageData) {
                $message = Spike::fromArray($messageData);

                //Fires filter action handler event
                $event = new FilterActionHandlerEvent($this, $message, $connection);
                $this->eventDispatcher->dispatch($event);

                if ($actionHandler = $event->getActionHandler()) {
                    $actionHandler->handle($message);
                }
            }
        })->then(null, function($exception) use ($connection){
            $this->eventDispatcher->dispatch(new Event(Events::CONNECTION_ERROR, $this, [
                'connection' => $connection,
                'exception' => $exception
            ]));
        });
    }

    /**
     * @return LoopInterface
     */
    public function getEventLoop()
    {
        return $this->eventLoop;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return Collection
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Gets the client by ID
     * @param string $id
     * @return null|ClientInterface
     */
    public function getClientById($id)
    {
        return $this->clients->filter(function(Client $client) use ($id){
            return $client->getId() === $id;
        })->first();
    }

    protected function initializeEvents()
    {
        $this->eventDispatcher->addSubscriber(new ServerListener());
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new Command\InitCommand($this),
            new Command\HelpCommand($this),
        ]);
    }
}
