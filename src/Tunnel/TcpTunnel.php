<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Tunnel;

use React\Socket\ConnectionInterface;

class TcpTunnel extends Tunnel
{
    /**
     * @var array
     */
    protected $host;

    public function __construct($serverPort, $host, ConnectionInterface $controlConnection = null)
    {
        $this->host = $host;
        parent::__construct($serverPort, $controlConnection);
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
            'host' => $this->host,
            'serverPort' => $this->serverPort
        ];
    }
}