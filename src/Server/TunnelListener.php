<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server;

use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use Spike\Server\Command\REQUESTPROXY;
use Spike\Socket\ServerInterface;
use Spike\Socket\TcpServer;

final class TunnelListener
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Tunnel
     */
    protected $tunnel;

    /**
     * @var ConnectionPool
     */
    protected $proxyConnections;

    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * @var LoopInterface
     */
    protected $loop;

    public function __construct(Client $client, Tunnel $tunnel, ?LoopInterface $loop = null)
    {
        $this->client = $client;
        $this->tunnel = $tunnel;
        $this->loop = $loop ?: Loop::get();
        $this->proxyConnections = new ConnectionPool();
    }

    /**
     * @return ConnectionPool
     */
    public function getProxyConnections(): ConnectionPool
    {
        return $this->proxyConnections;
    }

    public function listen()
    {
        $this->server = $this->createPublicServer($this->tunnel);
        $address = "0.0.0.0:{$this->tunnel->getPort()}";
        $this->server->configure([
            'address' => $address,
            'max_workers' => 4
        ]);
        $this->server->on('connection', [$this, 'handleConnection']);
        $this->server->serve();
    }

    public function handleConnection(ConnectionInterface $connection)
    {
        $proxyConnection = $this->proxyConnections->tryGet();
        if (null === $proxyConnection) {
            // request to spike client.
            $connection->pause();
            $this->client->getConnection()->executeCommand(new REQUESTPROXY($this->tunnel->getPort()));
            // suspend the listen.
            $this->server->pause();
        } else {
            $proxyConnection->pipe($connection);
        }
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