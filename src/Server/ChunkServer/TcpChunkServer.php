<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\ChunkServer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Slince\EventDispatcher\Event;
use Spike\Common\Exception\InvalidArgumentException;
use Spike\Common\Protocol\Spike;
use Spike\Common\Timer\TimersAware;
use Spike\Common\Tunnel\TcpTunnel;
use Spike\Common\Tunnel\TunnelInterface;
use Spike\Server\Client;
use Spike\Server\Event\Events;
use Spike\Server\Server;
use Spike\Server\ServerInterface;

class TcpChunkServer implements ChunkServerInterface
{
    use TimersAware;

    /**
     * @var Collection|PublicConnection[]
     */
    protected $publicConnections;

    /**
     * @var Socket
     */
    protected $socket;

    /**
     * @var TcpTunnel
     */
    protected $tunnel;

    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(Server $server, Client $client, TunnelInterface $tunnel)
    {
        $this->server = $server;
        $this->client = $client;
        $this->tunnel = $tunnel;
        $this->publicConnections = new ArrayCollection();
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
    public function getServer()
    {
        return $this->server;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function start()
    {
        $this->socket = new Socket($this->resolveListenAddress(), $this->server->getEventLoop());
        $this->socket->on('connection', function($connection){
            $publicConnection = new PublicConnection($connection);
            $this->publicConnections->add($publicConnection);
            $this->handlePublicConnection($publicConnection);
        });
        $this->addTimer(new Timer\ReviewPublicConnection($this));
    }

    /**
     * {@inheritdoc}
     */
    public function getClient()
    {
        return $this->client;
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
    public function stop()
    {
        //Close all public connection
        foreach ($this->publicConnections as $publicConnection) {
            $this->closePublicConnection($publicConnection);
        }
        $this->socket && $this->socket->close();
    }

    /**
     * Handles the public connection.
     *
     * @param PublicConnection $publicConnection
     * @codeCoverageIgnore
     */
    public function handlePublicConnection(PublicConnection $publicConnection)
    {
        //Request proxy to client
        $requestProxyMessage = new Spike('request_proxy', $this->tunnel->toArray(), [
            'public-connection-id' => $publicConnection->getId(),
        ]);
        $this->sendToClient($requestProxyMessage);
        //Fires 'request_proxy' event
        $this->server->getEventDispatcher()->dispatch(new Event(Events::REQUEST_PROXY, $this, [
            'message' => $requestProxyMessage,
        ]));
        //Pause the public connection
        $publicConnection->removeAllListeners();
        $publicConnection->pause();
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function setProxyConnection($publicConnectionId, ConnectionInterface $proxyConnection)
    {
        $publicConnection = $this->findPublicConnectionById($publicConnectionId);
        if (is_null($publicConnection)) {
            throw new InvalidArgumentException(sprintf('Cannot find the public connection "%s"', $publicConnectionId));
        }
        $startProxyMessage = new Spike('start_proxy');
        $proxyConnection->write($startProxyMessage);
        //Fires 'start_proxy' event
        $this->server->getEventDispatcher()->dispatch(new Event(Events::START_PROXY, $this, [
            'message' => $startProxyMessage,
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
            //There will be a bug if the tunnel server is closed before public connection
            $this->publicConnections && $this->publicConnections->removeElement($publicConnection);
        };
        $publicConnection->on('close', $handlePublicConnectionClose);
        $publicConnection->on('error', $handlePublicConnectionClose);

        //Handles proxy connection close
        $handleProxyConnectionClose = function () use ($publicConnection, &$handlePublicConnectionClose) {
            $publicConnection->removeListener('close', $handlePublicConnectionClose);
            $publicConnection->removeListener('error', $handlePublicConnectionClose);
            $publicConnection->end();
        };
        $proxyConnection->on('close', $handleProxyConnectionClose);
        $proxyConnection->on('error', $handleProxyConnectionClose);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventLoop()
    {
        return $this->server->getEventLoop();
    }

    /**
     * {@inheritdoc}
     */
    public function closePublicConnection(PublicConnection $publicConnection, $message = null)
    {
        $publicConnection->write($message ?: 'The chunk server is closed');
        $this->publicConnections->removeElement($publicConnection);
    }

    protected function sendToClient($data)
    {
        $this->client->getControlConnection()->write($data);
        $this->client->setActiveAt(new \DateTime());
    }

    /**
     * @param string $id
     *
     * @return null|PublicConnection
     */
    protected function findPublicConnectionById($id)
    {
        foreach ($this->publicConnections as $publicConnection) {
            if ($publicConnection->getId() === $id) {
                return $publicConnection;
            }
        }

        return null;
    }

    /**
     * Gets the server address to bind.
     *
     * @return string
     */
    protected function resolveListenAddress()
    {
        return "0.0.0.0:{$this->tunnel->getServerPort()}";
    }
}