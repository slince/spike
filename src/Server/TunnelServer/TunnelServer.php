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
use Spike\Server\TunnelServer\Timer\ReviewPublicConnection;
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
     * @var PublicConnectionCollection
     */
    protected $publicConnections;

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
        $this->publicConnections = new PublicConnectionCollection();
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function run()
    {
        $this->socket = new Socket($this->getListenAddress(), $this->loop);
        $this->socket->on('connection', function($connection){
            $publicConnection = new PublicConnection($connection);
            $this->publicConnections->add($publicConnection);
            $this->handlePublicConnection($publicConnection);
        });
        //Creates defaults timers
        foreach ($this->getDefaultTimers() as $timer) {
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
        //Close all public connection
        foreach ($this->publicConnections as $publicConnection) {
            $this->closePublicConnection($publicConnection, 'The tunnel server has been closed');
        }
        //Cancel all timers
        foreach ($this->timers as $timer) {
            $timer->cancel();
        }
        $this->publicConnections = null;
        $this->timers = null;
        $this->socket && $this->socket->close();
    }

    /**
     * Handles the public connection
     * @param PublicConnection $publicConnection
     * @codeCoverageIgnore
     */
    public function handlePublicConnection(PublicConnection $publicConnection)
    {
        $requestProxyMessage = new Spike('request_proxy', $this->tunnel->toArray(), [
            'Proxy-Connection-ID' => $publicConnection->getId()
        ]);
        $this->controlConnection->write($requestProxyMessage);
        //Fires 'request_proxy' event
        $this->getDispatcher()->dispatch(new Event(EventStore::REQUEST_PROXY, $this, [
            'message' => $requestProxyMessage
        ]));
        $publicConnection->removeAllListeners();
        $publicConnection->pause();
    }

    /**
     * Registers proxy connection
     * @param ConnectionInterface $proxyConnection
     * @param SpikeInterface $message
     * @codeCoverageIgnore
     */
    public function registerProxyConnection(ConnectionInterface $proxyConnection, SpikeInterface $message)
    {
        $connectionId = $message->getHeader('Proxy-Connection-ID');
        $publicConnection = $this->publicConnections->findById($connectionId);
        if (is_null($publicConnection)) {
            throw new InvalidArgumentException(sprintf('Cannot find the public connection "%s"', $connectionId));
        }
        $startProxyMessage = new Spike('start_proxy');
        $proxyConnection->write($startProxyMessage);
        //Fires 'start_proxy' event
        $this->getDispatcher()->dispatch(new Event(EventStore::REQUEST_PROXY, $this, [
            'message' => $startProxyMessage
        ]));
        //Resumes the public connection
        $publicConnection->resume();
        $publicConnection->pipe($proxyConnection);
        $proxyConnection->pipe($publicConnection->getConnection());
        $proxyConnection->write($publicConnection->getInitBuffer());

        //Handles public connection close
        $handlePublicConnectionClose = function() use ($proxyConnection, $publicConnection, &$handleProxyConnectionClose){
            $proxyConnection->removeListener('close', $handleProxyConnectionClose);
            $proxyConnection->removeListener('error', $handleProxyConnectionClose);
            $proxyConnection->end();
            echo 'proxy end';
            $this->publicConnections->removeElement($publicConnection);
        };
        $publicConnection->on('close', $handlePublicConnectionClose);
        $publicConnection->on('error', $handlePublicConnectionClose);

        //Handles proxy connection close
        $handleProxyConnectionClose = function () use ($publicConnection, &$handlePublicConnectionClose) {
            $publicConnection->removeListener('close', $handlePublicConnectionClose);
            $publicConnection->removeListener('error', $handlePublicConnectionClose);
            $publicConnection->end();
            echo 'tunnel end';
        };
        $proxyConnection->on('close', $handleProxyConnectionClose);
        $proxyConnection->on('error', $handleProxyConnectionClose);
    }

    /**
     * Gets the server
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
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
            new ReviewPublicConnection($this)
        ];
    }

    /**
     * Gets the socket of the tunnel server
     * @return Socket
     */
    public function getSocket()
    {
        return $this->socket;
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
    public function getPublicConnections()
    {
        return $this->publicConnections;
    }

    /**
     * {@inheritdoc}
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }
}