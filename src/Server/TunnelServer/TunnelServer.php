<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\Spike;
use Spike\Tunnel\TunnelInterface;

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

    /**
     * Handles the proxy connection
     * @param ConnectionInterface $connection
     */
    public function handleProxyConnection(ConnectionInterface $connection)
    {
        $proxyConnection = new ProxyConnection($connection);
        $this->proxyConnections[] = $proxyConnection;
        $this->controlConnection->write(new Spike('request_proxy', $this->tunnel->toArray(), [
            'Proxy-Connection-ID' => $proxyConnection->getId()
        ]));
        $connection->removeAllListeners();
        $connection->pause();
    }

    /**
     * @param ConnectionInterface $tunnelConnection
     * @param Spike $message
     */
    public function registerTunnelConnection(ConnectionInterface $tunnelConnection, Spike $message)
    {
        $this->tunnelConnections[] = $tunnelConnection;
        $proxyConnection = $this->findProxyConnection($message->getHeader('Proxy-Connection-ID'));
        if (!$proxyConnection) {
            throw new InvalidArgumentException("Cannot find proxy connection");
        }
        $tunnelConnection->write(new Spike('start_proxy'));
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