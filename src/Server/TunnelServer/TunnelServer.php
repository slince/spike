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
use Spike\Server\TunnelServer\Timer\ReviewProxyConnection;
use Spike\Timer\UseTimerTrait;
use Spike\Tunnel\TunnelInterface;
use Slince\Event\Dispatcher;
use Spike\Timer\TimerInterface;

abstract class TunnelServer implements TunnelServerInterface
{
    use UseTimerTrait;

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

    /**
     * @var TimerInterface[]
     */
    protected $timers;

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
        //Creates defaults timers
        $this->timers = $this->getDefaultTimers();
        foreach ($this->timers as $timer) {
            $this->addTimer($timer);
        }
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
        //Close all proxy connection
        foreach ($this->proxyConnections as $proxyConnection) {
            $this->closeProxyConnection($proxyConnection, 'The tunnel server has been closed');
        }
        //Cancel all timers
        foreach ($this->timers as $timer) {
            $timer->cancel();
        }
        $this->proxyConnections = null;
        $this->timers = null;
        $this->socket->close();
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
     * Creates default timers
     * @return TimerInterface[]
     */
    protected function getDefaultTimers()
    {
        return [
            new ReviewProxyConnection($this)
        ];
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
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * {@inheritdoc}
     */
    public function getProxyConnections()
    {
        return $this->proxyConnections;
    }

    /**
     * {@inheritdoc}
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }
}