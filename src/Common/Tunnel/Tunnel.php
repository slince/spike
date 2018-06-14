<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Common\Tunnel;

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