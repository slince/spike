<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\ChunkServer\Timer;

use Spike\Server\ChunkServer\ChunkServerInterface;
use Spike\Timer\PeriodicTimer as BasePeriodicTimer;

abstract class PeriodicTimer extends BasePeriodicTimer
{
    protected $tunnelServer;

    public function __construct(ChunkServerInterface $tunnelServer)
    {
        $this->tunnelServer = $tunnelServer;
    }

    /**
     * @return ChunkServerInterface
     */
    public function getTunnelServer()
    {
        return $this->tunnelServer;
    }
}