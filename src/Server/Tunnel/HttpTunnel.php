<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Tunnel;

use React\Socket\ConnectionInterface;

class HttpTunnel extends Tunnel
{
    /**
     * @var array
     */
    protected $hosts;

    public function __construct(ConnectionInterface $connection, $protocol, $remotePort, $hosts)
    {
        parent::__construct($connection, $protocol, $remotePort);
        $this->hosts = $hosts;
    }

    /**
     * @return mixed
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    public function supportHost($host)
    {
        return in_array($host, $this->hosts);
    }
}