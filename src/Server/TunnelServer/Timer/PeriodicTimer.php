<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer\Timer;

use Spike\Server\TunnelServer\TunnelServerInterface;
use Spike\Timer\PeriodicTimer as BasePeriodicTimer;

abstract class PeriodicTimer extends BasePeriodicTimer
{
    protected $tunnelServer;

    public function __construct(TunnelServerInterface $tunnelServer)
    {
        $this->tunnelServer = $tunnelServer;
    }

    /**
     * @return TunnelServerInterface
     */
    public function getTunnelServer()
    {
        return $this->tunnelServer;
    }
}