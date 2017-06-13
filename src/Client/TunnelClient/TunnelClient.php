<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\TunnelClient;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use Spike\Client\Tunnel\TunnelInterface;

abstract class TunnelClient implements TunnelClientInterface
{
    protected $tunnel;

    protected $address;

    protected $loop;

    protected $connector;

    protected $connection;

    public function __construct(TunnelInterface $tunnel, $address, LoopInterface $loop)
    {
        $this->tunnel = $tunnel;
        $this->address = $address;
        $this->loop = $loop;
        $this->connector = new Connector($this->loop);
        $this->connector->connect($this->address)->then(function(ConnectionInterface $localConnection){
            $this->handleLocalConnection($localConnection);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }
}