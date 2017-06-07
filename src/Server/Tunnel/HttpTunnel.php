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

    public function __construct($port, $hosts, ConnectionInterface $connection = null)
    {
        $this->hosts = $hosts;
        parent::__construct($port, $connection);
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