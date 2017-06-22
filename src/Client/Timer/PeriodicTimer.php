<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Timer;

use Spike\Client\Client;
use Spike\Timer\PeriodicTimer as BasePeriodicTimer;

abstract class PeriodicTimer extends BasePeriodicTimer
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}