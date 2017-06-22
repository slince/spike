<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Slince\Event\Dispatcher;
use Slince\Event\Event;
use Spike\Authentication\AuthenticationInterface;
use Spike\Exception\InvalidArgumentException;
use Spike\Exception\RuntimeException;
use Spike\Logger\Logger;
use Spike\Parser\SpikeParser;
use Spike\Protocol\Spike;
use Spike\Protocol\SpikeInterface;
use Spike\Server\Handler\HandlerInterface;
use Spike\Server\Timer\ReviewClient;
use Spike\Server\Timer\SummaryWatcher;
use Spike\Timer\MemoryWatcher;
use Spike\Timer\TimerInterface;
use Spike\Timer\UseTimerTrait;
use Spike\Tunnel\HttpTunnel;
use Spike\Tunnel\TunnelInterface;
use Spike\Server\TunnelServer\TunnelServerInterface;
use Spike\Utility;

class Server
{
    use UseTimerTrait;

    /**
     * The server host
     * @var string
     */
    protected $host;

    /**
     * The server port
     * @var int
     */
    protected $port;

    /**
     * The auth information
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var Socket
     */
    protected $socket;

    /**
     * @var TunnelServerInterface[]
     */
    protected $tunnelServers = [];

    /**
     * @var ClientCollection
     */
    protected $clients;

    protected $timers = [];

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(
        $address,
        AuthenticationInterface $authentication,
        LoopInterface $loop = null,
        Dispatcher $dispatcher = null
    ) {
        list($this->host, $this->port) = Utility::parseAddress($address);
        $this->authentication = $authentication;
        $this->loop = $loop ?: LoopFactory::create();
        $this->socket = new Socket($address, $this->loop);
        $this->dispatcher = $dispatcher ?: new Dispatcher();
        $this->clients = new ClientCollection();
        $this->tunnelServers = new TunnelServerCollection();
    }

    /**
     * Run the server
     */
    public function run()
    {
        $this->socket->on('connection', function(ConnectionInterface $connection){
            //Emit the event
            $this->dispatcher->dispatch(new Event(EventStore::ACCEPT_CONNECTION, $this, [
                'connection' => $connection
            ]));
            $this->handleControlConnection($connection);
        });
        $this->socket->on('error', function($exception){
            $this->dispatcher->dispatch(new Event(EventStore::SOCKET_ERROR, $this, [
                'exception' => $exception
            ]));
        });
        //Emit the event
        $this->dispatcher->dispatch(EventStore::SERVER_RUN);
        $this->timers = $this->getDefaultTimers();
        foreach ($this->timers as $timer) {
            $this->addTimer($timer);
        }
        $this->loop->run();
    }

    /**
     * Handles control connection
     * @param ConnectionInterface $connection
     */
    protected function handleControlConnection(ConnectionInterface $connection)
    {
        $parser = new SpikeParser();
        $connection->on('data', function($data) use($parser, $connection){
            $parser->pushIncoming($data);
            try {
                $messages = $parser->parse();
                foreach ($messages as $message) {
                    $message = Spike::fromString($message);
                    $this->dispatcher->dispatch(new Event(EventStore::RECEIVE_MESSAGE, $this, [
                        'message' => $message,
                        'connection' => $connection
                    ]));
                    $this->createMessageHandler($message, $connection)->handle($message);
                }
            } catch (RuntimeException $exception) {
                $this->dispatcher->dispatch(new Event(EventStore::CONNECTION_ERROR, $this, [
                    'connection' => $connection,
                    'exception' => $exception
                ]));
                $connection->end('Bad message');
            }
        });
        //When client be closed
        $connection->on('end', function() use ($connection){
            $client = $this->clients->findByConnection($connection);
            $this->closeClient($client);
        });
    }

    /**
     * Close the given client
     * @param Client $client
     */
    public function closeClient(Client $client)
    {
        $tunnelServers = $this->tunnelServers->filterByControlConnection($client->getControlConnection());
        $this->dispatcher->dispatch(new Event(EventStore::CLIENT_CLOSE, $this, [
            'client' => $client,
            'tunnelServers' => $tunnelServers
        ]));
        foreach ($tunnelServers as $tunnelServer) {
            //Close the tunnel server and removes it
            $tunnelServer->close();
            $this->tunnelServers->removeElement($tunnelServer);
        }
        $this->clients->removeElement($client); //Removes the client
    }

    /**
     * Creates a tunnel server for the tunnel
     * @param TunnelInterface $tunnel
     * @param ConnectionInterface $controlConnection
     */
    public function createTunnelServer(TunnelInterface $tunnel, ConnectionInterface $controlConnection)
    {
        if ($tunnel instanceof HttpTunnel) {
            $tunnelServer = new TunnelServer\HttpTunnelServer($this, $controlConnection, $tunnel, $this->loop);
        } else {
            $tunnelServer = new TunnelServer\TcpTunnelServer($this, $controlConnection, $tunnel, $this->loop);
        }
        $tunnelServer->run();
        $this->tunnelServers->add($tunnelServer);
    }

    /**
     * Gets the dispatcher
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Gets all clients
     * @return ClientCollection
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Gets all tunnel server
     * @return TunnelServerCollection
     */
    public function getTunnelServers()
    {
        return $this->tunnelServers;
    }

    /**
     * Gets the server host
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Gets the loop instance
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Sets a logger
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Gets the logger
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Gets the server port
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Gets the authentication
     * @return AuthenticationInterface
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * Creates the handler for the received message
     * @param SpikeInterface $message
     * @param ConnectionInterface $connection
     * @return HandlerInterface
     */
    protected function createMessageHandler($message, $connection)
    {
        switch ($message->getAction()) {
            case 'auth':
                $handler = new Handler\AuthHandler($this, $connection);
                break;
            case 'register_tunnel':
                $handler = new Handler\RegisterTunnelHandler($this, $connection);
                break;
            case 'register_proxy':
                $handler = new Handler\RegisterProxyHandler($this, $connection);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Cannot find handler for message type: "%s"',
                    get_class($message)
                ));
        }
        return $handler;
    }

    /**
     * Creates default timers
     * @return TimerInterface[]
     */
    protected function getDefaultTimers()
    {
        return [
            new ReviewClient($this),
            new MemoryWatcher($this->logger),
            new SummaryWatcher($this)
        ];
    }
}