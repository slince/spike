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

    public function __construct($port, $proxyHosts, ConnectionInterface $controlConnection = null)
    {
        $this->proxyHosts = $proxyHosts;
        parent::__construct($port, $controlConnection);
    }

    /**
     * Gets all proxy hosts
     * @return array
     */
    public function getProxyHosts()
    {
        return $this->proxyHosts;
    }

    /**
     * Checks whether the tunnel supports the host
     * @param string $host
     * @return bool
     */
    public function supportHost($host)
    {
        return in_array($host, $this->proxyHosts);
    }
}