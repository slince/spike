<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Spike\Protocol\Spike;
use Spike\Tunnel\TunnelInterface;

class TunnelServer implements TunnelServerInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $controlConnection;

    protected $loop;

    protected $socket;

    /**
     * @var TunnelInterface
     */
    protected $tunnel;

    public function __construct(ConnectionInterface $controlConnection, TunnelInterface $tunnel, $address, LoopInterface $loop)
    {
        $this->controlConnection = $controlConnection;
        $this->tunnel = $tunnel;
        $this->loop = $loop;
        $this->socket = new Socket($address, $loop);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->socket->on('connection', [$this, 'handleProxyConnection']);
    }

    public function handleProxyConnection(ConnectionInterface $proxyConnection)
    {
        $this->controlConnection->write(new Spike('request_proxy', $this->tunnel->toArray()));
        $proxyConnection->removeAllListeners();
        $this->tunnel->pipe($proxyConnection);
        $proxyConnection->pause();
    }

    /**
     *
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

    /**
     * {@inheritdoc}
     */
    public function pause()
    {
        $this->socket->pause();
    }

    /**
     * {@inheritdoc}
     */
    public function resume()
    {
        $this->socket->resume();
    }
}