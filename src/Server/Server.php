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
use Spike\Common\Protocol\Spike;
use Spike\Server\Event\Events;
use Spike\Server\Event\FilterActionHandlerEvent;
use Spike\Server\Listener\KernelListener;
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
     * @var Collection
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

    public function __construct(Configuration $configuration, LoopInterface $eventLoop)
    {
        $this->configuration = $configuration;
        $this->eventLoop = $eventLoop ?: Factory::create();
        $this->eventDispatcher = new Dispatcher();
        $this->chunkServers = new ArrayCollection();
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
     */
    public function createChunkServer($address)
    {

    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $server = new Socket($this->configuration->getAddress(), $this->eventLoop);
        $this->eventDispatcher->dispatch(Events::SERVER_RUN);
        $server->on('connection', [$this, 'handleControlConnection']);
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
     * {@inheritdoc}
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    protected function initializeEvents()
    {
        $this->eventDispatcher->addSubscriber(new KernelListener());
    }
}
