<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Tunnel;

use React\Socket\ConnectionInterface;

class TcpTunnel extends Tunnel
{
    protected $host;

    public function __construct($remotePort, $host, ConnectionInterface $controlConnection = null)
    {
        $this->host = $host;
        parent::__construct($remotePort, $controlConnection);
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    public function toArray()
    {
        return [
            'protocol' => 'tcp',
            'remotePort' => $this->remotePort,
            'host' => $this->host,
        ];
    }
}