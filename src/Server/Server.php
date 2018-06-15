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
use Spike\Common\Logger\Logger;
use Spike\Common\Protocol\Spike;
use Spike\Common\Timer\MemoryWatcher;
use Spike\Common\Timer\TimersAware;
use Spike\Server\ChunkServer\ChunkServerCollection;
use Spike\Server\ChunkServer\ChunkServerInterface;
use Spike\Server\Event\Events;
use Spike\Server\Event\FilterActionHandlerEvent;
use Spike\Server\Listener\LoggerListener;
use Spike\Server\Listener\ServerListener;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Server extends Application implements ServerInterface
{
    use TimersAware;

    /**
     * @var string
     */
    const LOGO = <<<EOT
 _____   _____   _   _   _    _____   _____  
/  ___/ |  _  \ | | | | / /  | ____| |  _  \ 
| |___  | |_| | | | | |/ /   | |__   | | | | 
\___  \ |  ___/ | | | |\ \   |  __|  | | | | 
 ___| | | |     | | | | \ \  | |___  | |_| | 
/_____/ |_|     |_| |_|  \_\ |_____| |_____/ 


EOT;

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
     * @var ChunkServerInterface[]|ChunkServerCollection
     */
    protected $chunkServers;

    /**
     * @var ClientInterface[]|Collection
     */
    protected $clients;

    /**
     * @var DispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var Logger
     */
    protected $logger;

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
    public function getHelp()
    {
        return static::LOGO.parent::getHelp();
    }

    /**
     * {@inheritdoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->logger = new Logger(
            $this->eventLoop,
            $this->getConfiguration()->getLogLevel(),
            $this->getConfiguration()->getLogFile(),
            $output
        );
        // Execute command if the command name is exists
        if ($this->getCommandName($input) ||
            true === $input->hasParameterOption(array('--help', '-h'), true)
        ) {
            $exitCode = parent::doRun($input, $output);
        } else {
            $exitCode = $this->start();
        }

        return $exitCode;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function start()
    {
        $server = new Socket($this->configuration->getAddress(), $this->eventLoop);
        $this->eventDispatcher->dispatch(Events::SERVER_RUN);
        $server->on('connection', [$this, 'handleControlConnection']);

        $this->initializeTimers();
        $this->eventLoop->run();

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function stopClient(ClientInterface $client)
    {
        $chunkServers = $this->chunkServers->filter(function(ChunkServerInterface $chunkServer) use ($client){
            return $client === $chunkServer->getClient();
        });
        $this->eventDispatcher->dispatch(new Event(Events::CLIENT_CLOSE, $this, [
            'client' => $client,
            'chunkServers' => $chunkServers,
        ]));
        foreach ($chunkServers as $chunkServer) {
            //Close the tunnel server and removes it
            $chunkServer->stop();
            $this->chunkServers->removeElement($chunkServer);
        }
        $client->close(); //Close the client
        $this->clients->removeElement($client); //Removes the client
    }

    /**
     * Handle control connection.
     *
     * @param ConnectionInterface $connection
     */
    public function handleControlConnection(ConnectionInterface $connection)
    {
        jsonBuffer($connection, function($messages) use ($connection){
            foreach ($messages as $messageData) {
                if (!$messageData) {
                    continue;
                }
                $message = Spike::fromArray($messageData);

                //Fires filter action handler event
                $event = new FilterActionHandlerEvent($this, $message, $connection);
                $this->eventDispatcher->dispatch($event);
                if ($actionHandler = $event->getActionHandler()) {
                    try {
                        $actionHandler->handle($message);
                    } catch (\Exception $exception) {
                        //Ignore bad message
                    }
                }
            }
        }, function($exception) use ($connection){
            $this->eventDispatcher->dispatch(new Event(Events::CONNECTION_ERROR, $this, [
                'connection' => $connection,
                'exception' => $exception,
            ]));
        });
        //Distinct
        $connection->on('close', function() use($connection){
            //If client has been registered and then close it.
            $client = $this->clients->filter(function(ClientInterface $client) use ($connection){
                return $client->getControlConnection() === $connection;
            })->first();
            if ($client) {
                $this->stopClient($client);
            } else {
                $connection->end();
            }
            $this->eventDispatcher->dispatch(new Event(Events::CLIENT_CLOSE, $this, [
                'connection' => $connection,
            ]));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getChunkServers()
    {
        return $this->chunkServers;
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
     * Gets all clients.
     *
     * @return Collection|ClientInterface[]
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Gets the client by ID.
     *
     * @param string $id
     *
     * @return null|ClientInterface
     */
    public function getClientById($id)
    {
        return $this->clients->filter(function(Client $client) use ($id){
            return $client->getId() === $id;
        })->first();
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    protected function initializeEvents()
    {
        $this->eventDispatcher->addSubscriber(new ServerListener());
        $this->eventDispatcher->addSubscriber(new LoggerListener($this));
    }

    /**
     * Creates default timers.
     *
     * @codeCoverageIgnore
     */
    protected function initializeTimers()
    {
        $this->addTimer(new Timer\ReviewClient($this));
        $this->addTimer(new Timer\SummaryWatcher($this));
        $this->addTimer(new MemoryWatcher($this->getLogger()));
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new Command\InitCommand($this),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOptions([
            new InputOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The configuration file, support json,ini,xml and yaml format'),
            new InputOption('address', 'a', InputOption::VALUE_REQUIRED, 'The server address'),
        ]);

        return $definition;
    }
}
