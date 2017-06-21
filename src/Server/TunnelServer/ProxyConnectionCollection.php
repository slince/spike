<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use Doctrine\Common\Collections\ArrayCollection;

class ProxyConnectionCollection extends ArrayCollection
{
    /**
     * Finds the connection by its id
     * @param string $id
     * @return ProxyConnection
     */
    public function findById($id)
    {
        foreach ($this as $proxyConnection) {
            if ($proxyConnection->getId() == $id) {
                return $proxyConnection;
            }
        }
        return null;
    }
}