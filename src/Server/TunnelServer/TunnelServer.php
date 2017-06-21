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

abstract class TunnelServer implements TunnelServerInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $controlConnection;

    /**
     * @var ProxyConnectionCollection
     */
    protected $proxyConnections;

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

    /**
     * @var LoopInterface
     */
    protected $loop;

    public function __construct(Server $server, ConnectionInterface $controlConnection, TunnelInterface $tunnel, LoopInterface $loop)
    {
        $this->server = $server;
        $this->controlConnection = $controlConnection;
        $this->tunnel = $tunnel;
        $this->loop = $loop;
        $this->socket = new Socket($this->getListenAddress(), $loop);
        $this->proxyConnections = new ProxyConnectionCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->socket->on('connection', function($connection){
            $proxyConnection = new ProxyConnection($connection);
            $this->proxyConnections->add($proxyConnection);
            $this->handleProxyConnection($proxyConnection);
        });
        $this->loop->addPeriodicTimer(2 * 1, [$this, 'handleProxyConnectionTimeout']);
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
        foreach ($this->proxyConnections as $proxyConnection) {
            $this->closeProxyConnection($proxyConnection, 'The tunnel server has been closed');
        }
        $this->proxyConnections = null;
        $this->socket->close();
    }

    /**
     * Close the connection if it does not respond for more than 60 seconds
     */
    public function handleProxyConnectionTimeout()
    {
        var_dump(count($this->proxyConnections));
        foreach ($this->proxyConnections as $key => $proxyConnection) {
            if ($proxyConnection->getWaitingDuration() > 60) {
                $this->closeProxyConnection($proxyConnection, 'Waiting for more than 60 seconds without responding');
                $this->proxyConnections->remove($key);
            }
        }
    }

    /**
     * Close the given proxy connection
     * @param ProxyConnection $proxyConnection
     * @param string $message
     */
    abstract protected function closeProxyConnection(ProxyConnection $proxyConnection, $message);

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
        $connectionId = $message->getHeader('Proxy-Connection-ID');
        $proxyConnection = $this->proxyConnections->findById($connectionId);
        if (is_null($proxyConnection)) {
            throw new InvalidArgumentException(sprintf('Cannot find the proxy connection "%s"', $connectionId));
        }
        $startProxyMessage = new Spike('start_proxy');
        $tunnelConnection->write($startProxyMessage);
        //Fires 'start_proxy' event
        $this->getDispatcher()->dispatch(new Event(EventStore::REQUEST_PROXY, $this, [
            'message' => $startProxyMessage
        ]));
        //Resumes the proxy connection
        $proxyConnection->resume();
        $proxyConnection->getConnection()->pipe($tunnelConnection);
        $tunnelConnection->pipe($proxyConnection->getConnection());
        $tunnelConnection->write($proxyConnection->getInitBuffer());

        //Handles proxy connection close
        $handleProxyConnectionClose = function() use ($tunnelConnection, $proxyConnection, &$handleTunnelConnectionClose){
            $tunnelConnection->removeListener('close', $handleTunnelConnectionClose);
            $tunnelConnection->removeListener('error', $handleTunnelConnectionClose);
            $tunnelConnection->end();
            echo 'proxy end';
            $this->proxyConnections->removeElement($proxyConnection);
        };
        $proxyConnection->getConnection()->on('close', $handleProxyConnectionClose);
        $proxyConnection->getConnection()->on('error', $handleProxyConnectionClose);

        //Handles tunnel connection close
        $handleTunnelConnectionClose = function () use ($proxyConnection, &$handleProxyConnectionClose) {
            $proxyConnection->getConnection()->removeListener('close', $handleProxyConnectionClose);
            $proxyConnection->getConnection()->removeListener('error', $handleProxyConnectionClose);
            $proxyConnection->getConnection()->end();
            echo 'tunnel end';
        };
        $tunnelConnection->on('close', $handleTunnelConnectionClose);
        $tunnelConnection->on('error', $handleTunnelConnectionClose);
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