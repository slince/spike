<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

class StartProxy extends SpikeRequest
{
    /**
     * @var array
     */
    protected $info;

    public function __construct(array $info, array $headers = [])
    {
        $this->info = $info;
        parent::__construct('start_proxy', $headers);
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    public static function parseBody($body)
    {
        return json_decode($body, true);
    }

    public function getBody()
    {
        return json_encode($this->info);
    }
}