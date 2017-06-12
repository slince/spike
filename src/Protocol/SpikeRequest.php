<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

class SpikeRequest extends Spike
{
    protected $body;

    public function getBody()
    {
        return $this->body;
    }

    public static function parseBody($body)
    {
        return $body;
    }
}
