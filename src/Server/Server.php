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
     * @var TunnelServerInterface
     */
    protected $tunnelServers = [];

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
        $this->socket->pause();
        //Emit the event
        $this->dispatcher->dispatch(EventStore::SERVER_RUN);
        $this->loop->run();
    }

    protected function handleConnection(ConnectionInterface $connection)
    {
        $buffer =  new SpikeBuffer($connection);
        $buffer->gather(function (BufferInterface $buffer) use ($connection) {
            $message = ProtocolFactory::create($buffer);
            $this->dispatcher->dispatch(new Event(EventStore::RECEIVE_MESSAGE, $this, [
                'message' => $message,
                'connection' => $connection
            ]));
            $this->createHandler($message, $connection)->handle($message);
            $buffer->flush(); //Flush the buffer and continue gather message
        });
    }

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
     * Creates the handler for the received message
     * @param $protocol
     * @param $connection
     * @return HandlerInterface
     */
    protected function createHandler($protocol, $connection)
    {
        if ($protocol instanceof RegisterTunnel) {
            $handler = new RegisterTunnelHandler($this, $connection);
        } else {
            throw new BadRequestException(sprintf('Cannot find handler for message type: "%s"',
                gettype($protocol)
            ));
        }
        return $handler;
    }
}