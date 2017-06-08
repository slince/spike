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
    protected $proxyHosts;

    public function __construct($port, $proxyHosts, ConnectionInterface $connection = null)
    {
        $this->proxyHosts = $proxyHosts;
        parent::__construct($port, $connection);
    }

    /**
     * @return array
     */
    public function getProxyHosts()
    {
        return $this->proxyHosts;
    }

    public function supportHost($host)
    {
        return in_array($host, $this->proxyHosts);
    }
}