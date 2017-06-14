<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Tunnel;

use React\Socket\ConnectionInterface;

class HttpTunnel extends Tunnel
{
    /**
     * The proxy hosts
     * @var array
     */
    protected $hosts;

    public function __construct($remotePort, $hosts, ConnectionInterface $controlConnection = null)
    {
        $this->hosts = $hosts;
        parent::__construct($remotePort, $controlConnection);
    }

    /**
     * @return mixed
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    public function getLocalHost($proxyHost)
    {
        return isset($this->hosts[$proxyHost]) ? $this->hosts[$proxyHost] : null;
    }

    /**
     * Checks whether support the proxy host
     * @param string $proxyHost
     * @return bool
     */
    public function supportProxyHost($proxyHost)
    {
        return isset($this->hosts[$proxyHost]);
    }

    public function toArray()
    {
        return [
            'protocol' => 'http',
            'remotePort' => $this->remotePort,
            'proxyHosts' => array_keys($this->hosts),
        ];
    }
}