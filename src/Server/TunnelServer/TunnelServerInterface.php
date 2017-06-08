<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use Spike\Server\Tunnel\TunnelInterface;

interface TunnelServerInterface
{
    /**
     * @return TunnelInterface
     */
    public function getTunnel();

    /**
     * Run the server
     */
    public function run();

    /**
     * Pause the server
     */
    public function pause();

    /**
     * Resumes the server
     */
    public function resume();
}