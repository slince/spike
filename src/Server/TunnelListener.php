<?php

namespace Spike\Server;

use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use Spike\Socket\ServerInterface;
use Spike\Socket\TcpServer;

final class TunnelListener
{
    /**
     * @var Tunnel
     */
    protected $tunnel;

    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * @var LoopInterface
     */
    protected $loop;

    public function __construct(Tunnel $tunnel, ?LoopInterface $loop = null)
    {
        $this->tunnel = $tunnel;
        $this->loop = $loop ?: Loop::get();
    }

    public function listen()
    {
        $server = $this->createPublicServer($this->tunnel);
        $address = "0.0.0.0:{$this->tunnel->getPort()}";
        $server->configure([
            'address' => $address,
            'max_workers' => 4
        ]);
        $server->serve();
    }

    protected function createPublicServer(Tunnel $tunnel)
    {
        if ('tcp' === ($scheme = $tunnel->getScheme())) {
            $server = new TcpServer($this->loop);
        } elseif ('udp' === $scheme) {
            $server = null;
        }
        return $server;
    }
}