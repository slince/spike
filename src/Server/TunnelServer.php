<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Spike\Server\Tunnel\TunnelInterface;

abstract class TunnelServer
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
    }

    abstract protected function handleConnection(ConnectionInterface $connection);
}