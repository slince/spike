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
use Spike\Server\Connection\ProxyConnectionPool;
use Spike\Server\Connection\PublicConnection;
use Spike\Server\Connection\PublicConnectionPool;
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
     * @var ServerInterface
     */
    protected $server;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var ProxyConnectionPool
     */
    protected $proxyConnections;

    /**
     * @var PublicConnectionPool
     */
    protected $publicConnections;

    public function __construct(Client $client, Tunnel $tunnel, ?LoopInterface $loop = null)
    {
        $this->client = $client;
        $this->tunnel = $tunnel;
        $this->loop = $loop ?: Loop::get();
        $this->proxyConnections = new ProxyConnectionPool();
        $this->publicConnections = new PublicConnectionPool();
    }

    /**
     * @return ProxyConnectionPool
     */
    public function getProxyConnections(): ProxyConnectionPool
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

    /**
     * {@internal}
     */
    public function handleConnection(ConnectionInterface $connection)
    {
        $publicConnection = new PublicConnection($connection);
        $this->publicConnections->add($publicConnection);
        $proxyConnection = $this->proxyConnections->tryGet();
        if (null === $proxyConnection) {
            // request to spike client.
            $publicConnection->pause();
            $this->client->getConnection()->executeCommand(new REQUESTPROXY($this->tunnel->getPort()));
        } else {
            $proxyConnection->pipe($publicConnection);
        }
    }

    public function handle()
    {

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