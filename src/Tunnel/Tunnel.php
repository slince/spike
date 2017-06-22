<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Tunnel;

use React\Socket\ConnectionInterface;
use Spike\Exception\InvalidArgumentException;

abstract class Tunnel implements TunnelInterface
{
    /**
     * The tunnel server port
     * @var int
     */
    protected $serverPort;

    public function __construct($serverPort)
    {
        $this->serverPort = $serverPort;
    }


    /**
     * {@inheritdoc}
     */
    public function getServerPort()
    {
        return $this->serverPort;
    }

    /**
     * {@inheritdoc}
     */
    public function match($info)
    {
        return $this->getServerPort() == $info['serverPort'];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return json_encode($this->toArray());
    }
}