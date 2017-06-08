<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
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
        $this->pause();
    }

    /**
     * Gets the tunnel
     * @return TunnelInterface
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }

    public function pause()
    {
        $this->socket->pause();
    }

    public function resume()
    {
        $this->socket->resume();
    }

    public function run()
    {
        $this->resume();
    }

    abstract protected function handleConnection(ConnectionInterface $connection);

}