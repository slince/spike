<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Client\Worker;

use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use function Slince\Common\jsonBuffer;
use Spike\Client\Client;
use Spike\Common\Protocol\Spike;
use Spike\Common\Tunnel\TcpTunnel;
use Spike\Common\Tunnel\TunnelInterface;

class TcpWorker implements WorkerInterface
{
    /**
     * @var TcpTunnel
     */
    protected $tunnel;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ConnectionInterface
     */
    protected $proxyConnection;

    /**
     * @var ConnectionInterface
     */
    protected $localConnection;

    /**
     * @var string
     */
    protected $publicConnectionId;

    public function __construct(Client $client, TunnelInterface $tunnel, $publicConnectionId)
    {
        $this->client = $client;
        $this->tunnel = $tunnel;
        $this->publicConnectionId = $publicConnectionId;
    }

    /**
     * @codeCoverageIgnore
     */
    public function start()
    {
        $connector = new Connector($this->client->getEventLoop());
        $connector->connect($this->client->getConfiguration()->getServerAddress())
            ->then([$this, 'handleProxyConnection']);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTargetHost()
    {
        return $this->tunnel->getHost();
    }

    /**
     * Handles the proxy connection
     * @param ConnectionInterface $connection
     * @codeCoverageIgnore
     */
    public function handleProxyConnection(ConnectionInterface $connection)
    {
        $this->proxyConnection = $connection;

        jsonBuffer($connection, function($messages) use ($connection){
            $message = Spike::fromArray(reset($messages));
            if ($message && $messages->getAction() === 'start_proxy') {
                $connection->removeAllListeners('data');
                $localAddress = $this->resolveTargetHost();
                $this->connectLocalHost($localAddress);
            }
        });
        //Register proxy connection
        $connection->write(new Spike('register_proxy', $this->tunnel->toArray(), [
            'public-connection-id' => $this->publicConnectionId
        ]));
    }

    /**
     * Connect the local server
     * @param string $address
     * @codeCoverageIgnore
     */
    protected function connectLocalHost($address)
    {
        $localConnector = new Connector($this->client->getEventLoop());
        $localConnector->connect($address)->then([$this, 'handleLocalConnection'],
            [$this, 'handleConnectLocalError']
        );
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function handleLocalConnection(ConnectionInterface $localConnection)
    {
        $localConnection->pipe($this->proxyConnection);
        $this->proxyConnection->pipe($localConnection);
//        $localConnection->write($this->initBuffer);

        //Handles the local connection close
        $handleLocalConnectionClose = function(){
            $this->stop();
        };
        $localConnection->on('close', $handleLocalConnectionClose);
        $localConnection->on('error', $handleLocalConnectionClose);

        //Handles the proxy connection close
        $handleProxyConnectionClose = function(){
            $this->stop();
        };
        $this->proxyConnection->on('close', $handleProxyConnectionClose);
        $this->proxyConnection->on('error', $handleProxyConnectionClose);
    }

    /**
     * {@inheritdoc}
     */
    public function handleConnectLocalError(\Exception $exception)
    {
        $this->proxyConnection->end($exception->getMessage());
        $this->stop();
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        if ($this->localConnection) {
            $this->localConnection->end();
        }
        if ($this->proxyConnection) {
            $this->proxyConnection->end();
        }
        $this->client->getWorkers()->removeElement($this);
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
    public static function support(TunnelInterface $tunnel)
    {
        return $tunnel instanceof TcpTunnel;
    }
}