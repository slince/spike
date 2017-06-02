<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Subscriber;

use Slince\Event\SubscriberInterface;
use Spike\Server;

abstract class Subscriber implements SubscriberInterface
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