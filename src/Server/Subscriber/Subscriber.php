<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Subscriber;

use Slince\Event\SubscriberInterface;
use Spike\Server\Application;

abstract class Subscriber implements SubscriberInterface
{
    /**
     * @var Application
     */
    protected $server;

    public function __construct(Application $server)
    {
        $this->server = $server;
    }

    /**
     * @return Application
     */
    public function getServer()
    {
        return $this->server;
    }
}