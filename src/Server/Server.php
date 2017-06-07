<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use Cake\Collection\Collection;
use function foo\func;
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Slince\Event\Dispatcher;
use Slince\Event\Event;
use Spike\Buffer\BufferInterface;
use Spike\Buffer\HttpBuffer;
use Spike\Buffer\SpikeBuffer;
use Spike\Exception\BadRequestException;
use Spike\Exception\InvalidArgumentException;
use Spike\Exception\RuntimeException;
use Spike\Protocol\HttpRequest;
use Spike\Protocol\RegisterHostRequest;
use Spike\Protocol\ProxyResponse;
use Spike\Protocol\ProtocolFactory;
use Spike\Server\Handler\HandlerInterface;
use Spike\Server\Handler\ProxyRequestHandler;
use Spike\Server\Handler\ProxyResponseHandler;
use Spike\Server\Handler\RegisterHostHandler;
use Spike\Tunnel\HttpTunnel;
use Spike\Tunnel\TunnelInterface;

class Server
{
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
     * @var ProxyConnection[]
     */
    protected $proxyConnections = [];

    /**
     * @var ProxyHost[]
     */
    protected $proxyHosts = [];

    /**
     * @var TunnelInterface[]
     */
    protected $tunnels = [];

    protected $host;

    protected $port;

    public function __construct($address, LoopInterface $loop = null, Dispatcher $dispatcher = null)
    {
        $this->loop = $loop ?: LoopFactory::create();
        $this->socket = new Socket($address, $this->loop);
        $this->dispatcher = $dispatcher ?: new Dispatcher();
        list($this->host, $this->port) = $this->parseAddress($address);
    }

    protected function parseAddress($address)
    {
        $parts = array_filter(explode($address, ':'));
        if (count($parts) !== 2) {
            throw new InvalidArgumentException(sprintf('The address "%s" is invalid', $address));
        }
        return $parts;
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
        $handle = function ($data) use ($connection, &$handle) {
            try{
                $connection->removeAllListeners();
                $firstLineMessage = strstr($data, "\r\n", true);
                if (strpos($firstLineMessage, 'Spike') !== false) {
                    $buffer = new SpikeBuffer($connection);
                } else {
                    throw new BadRequestException("Unsupported protocol");
                }
                $buffer->gather(function (BufferInterface $buffer) use ($connection, $handle) {
                    $message = ProtocolFactory::create($buffer);
                    $this->dispatcher->dispatch(new Event(EventStore::RECEIVE_MESSAGE, $this, [
                        'message' => $message,
                        'connection' => $connection
                    ]));
                    $this->createHandler($message, $connection)->handle($message);
                    $buffer->flush(); //Flush the buffer and continue gather message
                    $connection->once('data', $handle); //An loop has been end
                });
                $connection->emit('data', [$data]);
            } catch (RuntimeException $exception) {
                $this->dispatcher->dispatch(new Event(EventStore::CONNECTION_ERROR, $this, [
                    'connection' => $connection,
                    'exception' => $exception,
                ]));
            }
        };
        $connection->once('data', $handle);
    }

    protected function createSocketForTunnel(TunnelInterface $tunnel)
    {
        $socket = new Socket("{$this->host}:{$tunnel->getRemotePort()}", $this->loop);
        if ($tunnel instanceof HttpTunnel) {

        }
        $socket->on('connection', function(ConnectionInterface $connection){

        });
    }

    /**
     * Gets all proxy connections
     * @return ProxyConnection[]
     */
    public function getProxyConnections()
    {
        return $this->proxyConnections;
    }

    /**
     * Gets the proxy hosts
     * @return ProxyHost[]
     */
    public function getProxyHosts()
    {
        return $this->proxyHosts;
    }

    /**
     * Sets the proxy hosts of the server
     * @param Collection $proxyHosts
     */
    public function setProxyHosts($proxyHosts)
    {
        $this->proxyHosts = $proxyHosts;
    }

    /**
     * Adds a proxy host record
     * @param ProxyHost $proxyHost
     */
    public function addProxyHost(ProxyHost $proxyHost)
    {
        $this->proxyHosts[] = $proxyHost;
    }

    /**
     * Adds some proxy hosts
     * @param ProxyHost[] $proxyHosts
     */
    public function addProxyHosts($proxyHosts)
    {
        $this->proxyHosts += $proxyHosts;
    }

    /**
     * Finds the proxy host for the given host
     * @param string $host
     * @return null|ProxyHost
     */
    public function findProxyHost($host)
    {
        foreach ($this->proxyHosts as $proxyHost) {
            if ($proxyHost->getHost() == $host) {
                return $proxyHost;
            }
        }
        return null;
    }

    public function addProxyConnection(ProxyConnection $proxyConnection)
    {
        $this->proxyConnections[] = $proxyConnection;
    }

    /**
     * Finds the proxy connection by given id
     * @param string $connectionId
     * @return null|ProxyConnection
     */
    public function findProxyConnection($connectionId)
    {
        foreach ($this->proxyConnections as $proxyConnection) {
            if ($proxyConnection->getId() == $connectionId) {
                return $proxyConnection;
            }
        }
        return null;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Creates the handler for the received message
     * @param $protocol
     * @param $connection
     * @return HandlerInterface
     */
    protected function createHandler($protocol, $connection)
    {
        if ($protocol instanceof RegisterHostRequest) {
            $handler = new RegisterHostHandler($this, $connection);
        } elseif ($protocol instanceof HttpRequest) {
            $handler = new ProxyRequestHandler($this, $connection);
        } elseif ($protocol instanceof ProxyResponse) {
            $handler = new ProxyResponseHandler($this, $connection);
        } else {
            throw new BadRequestException(sprintf('Cannot find handler for message type: "%s"',
                gettype($protocol)
            ));
        }
        return $handler;
    }
}