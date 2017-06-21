<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Timer;

use Spike\Server\Server;
use Spike\Timer\PeriodicTimer as BasePeriodicTimer;

abstract class PeriodicTimer extends BasePeriodicTimer
{
    /**
     * @var Server
     */
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }
}