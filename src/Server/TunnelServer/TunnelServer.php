<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Slince\Event\Event;
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\Spike;
use Spike\Protocol\SpikeInterface;
use Spike\Server\EventStore;
use Spike\Server\Server;
use Spike\Tunnel\TunnelInterface;
use Slince\Event\Dispatcher;

class TunnelServer implements TunnelServerInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $controlConnection;

    /**
     * @var ProxyConnection[]
     */
    protected $proxyConnections = [];

    /**
     * @var ConnectionInterface[]
     */
    protected $tunnelConnections = [];

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var Socket
     */
    protected $socket;

    /**
     * @var TunnelInterface
     */
    protected $tunnel;

    /**
     * @var Server
     */
    protected $server;

    public function __construct(Server $server, ConnectionInterface $controlConnection, TunnelInterface $tunnel, LoopInterface $loop)
    {
        $this->server = $server;
        $this->controlConnection = $controlConnection;
        $this->tunnel = $tunnel;
        $this->loop = $loop;
        $this->socket = new Socket($this->getListenAddress(), $loop);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->socket->on('connection', function($connection){
            $proxyConnection = new ProxyConnection($connection);
            $this->proxyConnections[] = $proxyConnection;
            $this->handleProxyConnection($proxyConnection);
        });
    }

    /**
     * Gets the event dispatcher
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->server->getDispatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->closeAllProxyConnections();
        $this->socket->close();
        $this->proxyConnections = null;
    }

    /**
     * Close all proxy connections
     */
    protected function closeAllProxyConnections()
    {
        foreach ($this->proxyConnections as $proxyConnection) {
            $proxyConnection->getConnection()->end('The tunnel server has been closed');
        }
    }

    /**
     * Handles the proxy connection
     * @param ProxyConnection $proxyConnection
     */
    public function handleProxyConnection(ProxyConnection $proxyConnection)
    {
        $requestProxyMessage = new Spike('request_proxy', $this->tunnel->toArray(), [
            'Proxy-Connection-ID' => $proxyConnection->getId()
        ]);
        $this->controlConnection->write($requestProxyMessage);
        //Fires 'request_proxy' event
        $this->getDispatcher()->dispatch(new Event(EventStore::REQUEST_PROXY, $this, [
            'message' => $requestProxyMessage
        ]));
        $proxyConnection->getConnection()->removeAllListeners();
        $proxyConnection->pause();
    }

    /**
     * Registers tunnel connection
     * @param ConnectionInterface $tunnelConnection
     * @param SpikeInterface $message
     */
    public function registerTunnelConnection(ConnectionInterface $tunnelConnection, SpikeInterface $message)
    {
        $this->tunnelConnections[] = $tunnelConnection;
        $proxyConnection = $this->findProxyConnection($message->getHeader('Proxy-Connection-ID'));
        if (!$proxyConnection) {
            throw new InvalidArgumentException("Cannot find proxy connection");
        }
        $startProxyMessage = new Spike('start_proxy');
        $tunnelConnection->write($startProxyMessage);
        //Fires 'start_proxy' event
        $this->getDispatcher()->dispatch(new Event(EventStore::REQUEST_PROXY, $this, [
            'message' => $startProxyMessage
        ]));
        $proxyConnection->resume();
        $proxyConnection->getConnection()->pipe($tunnelConnection);
        $tunnelConnection->pipe($proxyConnection->getConnection());
        $tunnelConnection->write($proxyConnection->getInitBuffer());
        $proxyConnection->getConnection()->on('close', function () use ($tunnelConnection) {
            $tunnelConnection->end();
        });
        $tunnelConnection->on('close', function () use ($proxyConnection) {
            $proxyConnection->getConnection()->end();
        });
    }

    /**
     * Finds the connection by id
     * @param  string $connectionId
     * @return ProxyConnection
     */
    protected function findProxyConnection($connectionId)
    {
        foreach ($this->proxyConnections as $proxyConnection) {
            if ($proxyConnection->getId() == $connectionId) {
                return $proxyConnection;
            }
        }
        throw new InvalidArgumentException(sprintf('Cannot find the proxy connection "%s"', $connectionId));
    }

    /**
     * Gets the server address to bind
     * @return string
     */
    protected function getListenAddress()
    {
        return "{$this->server->getHost()}:{$this->tunnel->getServerPort()}";
    }

    /**
     * {@inheritdoc}
     */
    public function getControlConnection()
    {
        return $this->controlConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }
}