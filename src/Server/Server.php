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
use Spike\Buffer\BufferInterface;
use Spike\Buffer\SpikeBuffer;
use Spike\Exception\BadRequestException;
use Spike\Exception\InvalidArgumentException;
use Spike\Exception\RuntimeException;
use Spike\Protocol\ProtocolFactory;
use Spike\Protocol\RegisterTunnel;
use Spike\Protocol\Spike;
use Spike\Protocol\SpikeInterface;
use Spike\Server\Handler\HandlerInterface;
use Spike\Server\Handler\RegisterTunnelHandler;
use Spike\Server\Tunnel\HttpTunnel;
use Spike\Server\Tunnel\TunnelInterface;
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
            $this->handleConnection($connection);
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

    protected function handleConnection(ConnectionInterface $connection)
    {
        $buffer = new SpikeBuffer($connection);
        $buffer->gather(function (BufferInterface $buffer) use ($connection) {
            $message = Spike::fromString($buffer);
            var_dump(strval($buffer));
            echo PHP_EOL;

            $this->dispatcher->dispatch(new Event(EventStore::RECEIVE_MESSAGE, $this, [
                'message' => $message,
                'connection' => $connection
            ]));
            $this->createMessageHandler($message, $connection)->handle($message);
            $buffer->flush(); //Flush the buffer and continue gather message
        });
    }

    /**
     * Creates a tunnel server for the tunnel
     * @param TunnelInterface $tunnel
     */
    public function createTunnelServer(TunnelInterface $tunnel)
    {
        if ($tunnel instanceof HttpTunnel) {
            $tunnelServer = new TunnelServer\HttpTunnelServer($tunnel, "{$this->host}:{$tunnel->getPort()}", $this->loop);
        } else {
            $tunnelServer = new TunnelServer\TcpTunnelServer($tunnel, "{$this->host}:{$tunnel->getPort()}", $this->loop);
        }
        $this->tunnelServers[] = $tunnelServer;
        $tunnelServer->run();
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param Client[] $clients
     */
    public function setClients($clients)
    {
        $this->clients = $clients;
    }

    /**
     * @return Client[]
     */
    public function getClients()
    {
        return $this->clients;
    }

    public function addClient(Client $client)
    {
        $this->clients[] = $client;
    }

    /**
     * @return TunnelServerInterface[]
     */
    public function getTunnelServers()
    {
        return $this->tunnelServers;
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
            case 'start_proxy':
                $handler = new Handler\StartProxyHandler($this);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Cannot find handler for message type: "%s"',
                    get_class($message)
                ));
        }
        return $handler;
    }
}