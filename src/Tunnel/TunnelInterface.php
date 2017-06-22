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
     * Gets the tunnel protocol
     * @return string
     */
    public function getProtocol();

    /**
     * Gets the tunnel info
     * @return array
     */
    public function toArray();

    /**
     * Get the summary of the tunnel
     * @return string
     */
    public function __toString();

    /**
     * Checks whether the tunnel match the info
     * @param array $info
     * @return boolean
     */
    public function match($info);

}