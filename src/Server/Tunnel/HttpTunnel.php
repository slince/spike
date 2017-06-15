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
    public function supportProxyHost($host)
    {
        return in_array($host, $this->proxyHosts);
    }

    /**
     * {@inheritdoc}
     */
    public function match($info)
    {
        return parent::match($info)
            &&  (!isset($info['proxyHost'])
                || $this->supportProxyHost($info['proxyHost']));
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'hosts' => $this->proxyHosts,
            'port' => $this->port
        ];
    }
}