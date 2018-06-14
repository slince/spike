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

class TcpTunnel extends Tunnel
{
    /**
     * @var string
     */
    protected $host;

    public function __construct($serverPort, $host)
    {
        $this->host = $host;
        parent::__construct($serverPort);
    }

    /**
     * Gets the local host
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'protocol' => $this->getProtocol(),
            'host' => $this->host,
            'serverPort' => $this->serverPort
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocol()
    {
        return 'tcp';
    }

}