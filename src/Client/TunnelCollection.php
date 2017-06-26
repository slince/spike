<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client;

use Doctrine\Common\Collections\ArrayCollection;
use Spike\Tunnel\TunnelInterface;

class TunnelCollection extends ArrayCollection
{
    /**
     * Finds the matching tunnel
     * @param array $tunnelInfo
     * @return false|TunnelInterface
     */
    public function findByInfo($tunnelInfo)
    {
        return $this->filter(function(TunnelInterface $tunnel) use ($tunnelInfo){
            return $tunnel->match($tunnelInfo);
        })->first();
    }
}