<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;

class TunnelClient
{
    protected $address;

    protected $connector;

    public function __construct($address, $connection, LoopInterface $loop)
    {
        $this->address = $address;
        $this->loop = $loop;
        $this->connector = new Connector($this->loop);
        $this->connector->connect($this->address)->then(function(ConnectionInterface $localConnection) use($connection){
            $connection->pipe($localConnection);
        });
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