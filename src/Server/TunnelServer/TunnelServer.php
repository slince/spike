<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Spike\Protocol\RequestProxy;
use Spike\Server\Tunnel\TunnelInterface;

abstract class TunnelServer implements TunnelServerInterface
{
    protected $address;

    protected $loop;

    protected $socket;

    /**
     * @var TunnelInterface
     */
    protected $tunnel;

    public function __construct(TunnelInterface $tunnel, $address, LoopInterface $loop)
    {
        $this->tunnel = $tunnel;
        $this->address = $address;
        $this->loop = $loop;
        $this->socket = new Socket($this->address, $loop);
        $this->socket->on('connection', function(ConnectionInterface $connection){
            $this->handleConnection($connection);
        });
    }

    protected function handleConnection(ConnectionInterface $connection)
    {
        $this->getTunnel()->getControlConnection()->write(new RequestProxy());
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

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->resume();
    }
}