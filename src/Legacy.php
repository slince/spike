<?php
/**
 * Created by PhpStorm.
 * User: taosikai
 * Date: 2017/6/8
 * Time: 11:15
 */

namespace Spike;


class Legacy
{
    /**
     * Gets all proxy connections
     * @return ProxyConnection[]
     */
    public function getProxyConnections()
    {
        return $this->proxyConnections;
    }

    /**
     * Gets the proxy hosts
     * @return ProxyHost[]
     */
    public function getProxyHosts()
    {
        return $this->proxyHosts;
    }

    /**
     * Sets the proxy hosts of the server
     * @param Collection $proxyHosts
     */
    public function setProxyHosts($proxyHosts)
    {
        $this->proxyHosts = $proxyHosts;
    }

    /**
     * Adds a proxy host record
     * @param ProxyHost $proxyHost
     */
    public function addProxyHost(ProxyHost $proxyHost)
    {
        $this->proxyHosts[] = $proxyHost;
    }

    /**
     * Adds some proxy hosts
     * @param ProxyHost[] $proxyHosts
     */
    public function addProxyHosts($proxyHosts)
    {
        $this->proxyHosts += $proxyHosts;
    }

    /**
     * Finds the proxy host for the given host
     * @param string $host
     * @return null|ProxyHost
     */
    public function findProxyHost($host)
    {
        foreach ($this->proxyHosts as $proxyHost) {
            if ($proxyHost->getHost() == $host) {
                return $proxyHost;
            }
        }
        return null;
    }

    public function addProxyConnection(ProxyConnection $proxyConnection)
    {
        $this->proxyConnections[] = $proxyConnection;
    }

    /**
     * Finds the proxy connection by given id
     * @param string $connectionId
     * @return null|ProxyConnection
     */
    public function findProxyConnection($connectionId)
    {
        foreach ($this->proxyConnections as $proxyConnection) {
            if ($proxyConnection->getId() == $connectionId) {
                return $proxyConnection;
            }
        }
        return null;
    }
}