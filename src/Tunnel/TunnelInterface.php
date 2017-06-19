<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Tunnel;

interface TunnelInterface
{
    /**
     * Gets the tunnel server port
     * @return int
     */
    public function getServerPort();

    /**
     * Gets the tunnel info
     * @return array
     */
    public function toArray();

    /**
     * Checks whether the tunnel match the info
     * @param array $info
     * @return boolean
     */
    public function match($info);

}