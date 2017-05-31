<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use Cake\Collection\Collection;
use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Slince\Event\Dispatcher;
use Slince\Event\Event;
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\RegisterHostRequest;
use Spike\Protocol\ProxyResponse;
use Spike\ProtocolFactory;
use Spike\Server\Handler\HandlerInterface;
use Spike\Server\Handler\ProxyRequestHandler;
use Spike\Server\Handler\ProxyResponseHandler;
use Spike\Server\Handler\RegisterHostHandler;

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

    public function __construct($address, LoopInterface $loop = null, Dispatcher $dispatcher = null)
    {
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
            $connection->on('data', function($data) use ($connection){
                $message = ProtocolFactory::create($data);
                $this->dispatcher->dispatch(new Event(EventStore::RECEIVE_MESSAGE, $this, [
                    'message' => $message,
                    'connection' => $connection
                ]));
                $this->createHandler($message, $connection)->handle($message);
            });
        });
        $this->socket->on('error', function($exception){
            $this->dispatcher->dispatch(new Event(EventStore::SOCKET_ERROR, $this, [
                'exception' => $exception
            ]));
        });
        $this->loop->run();
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
        } elseif ($protocol instanceof RequestInterface) {
            $handler = new ProxyRequestHandler($this, $connection);
        } elseif ($protocol instanceof ProxyResponse) {
            $handler = new ProxyResponseHandler($this, $connection);
        } else {
            throw new InvalidArgumentException(sprintf('Cannot find handler for message type: "%s"',
                gettype($protocol)
            ));
        }
        return $handler;
    }
}