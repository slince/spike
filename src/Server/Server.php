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
use Spike\Exception\InvalidArgumentException;
use Spike\Parser\SpikeParser;
use Spike\Protocol\Spike;
use Spike\Protocol\SpikeInterface;
use Spike\Server\Handler\HandlerInterface;
use Spike\Tunnel\HttpTunnel;
use Spike\Tunnel\TunnelInterface;
use Spike\Server\TunnelServer\TunnelServerInterface;
use Spike\Utility;

class Server
{
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
     * @var ConnectionInterface
     */
    protected $controlConnections = [];

    /**
     * @var Client[]
     */
    protected $clients;

    public function __construct($address, LoopInterface $loop = null, Dispatcher $dispatcher = null)
    {
        list($this->host, $this->port) = Utility::parseAddress($address);
        $this->loop = $loop ?: LoopFactory::create();
        $this->socket = new Socket($address, $this->loop);
        $this->dispatcher = $dispatcher ?: new Dispatcher();
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
            $messages = $parser->parse();
            foreach ($messages as $message) {
                echo $message, PHP_EOL,PHP_EOL;
                $message = Spike::fromString($message);
                $this->dispatcher->dispatch(new Event(EventStore::RECEIVE_MESSAGE, $this, [
                    'message' => $message,
                    'connection' => $connection
                ]));
                $this->createMessageHandler($message, $connection)->handle($message);
            }
        });
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
        $this->tunnelServers[] = $tunnelServer;
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
     * @return Client[]
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Adds a client
     * @param Client $client
     */
    public function addClient(Client $client)
    {
        $this->clients[] = $client;
    }

    /**
     * Gets all tunnel server
     * @return TunnelServerInterface[]
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
     * Gets the server port
     * @return int
     */
    public function getPort()
    {
        return $this->port;
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
}