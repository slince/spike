<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

class RequestProxy extends SpikeRequest
{
    public function __construct(array $headers = [])
    {
        parent::__construct('register_proxy', $headers);
    }
}