<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Subscriber;

use Slince\Event\SubscriberInterface;
use Spike\Client\Application;

abstract class Subscriber implements SubscriberInterface
{
    /**
     * @var Application
     */
    protected $client;

    public function __construct(Application $client)
    {
        $this->client = $client;
    }

    /**
     * @return Application
     */
    public function getClient()
    {
        return $this->client;
    }
}