<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

class AuthRequest extends SpikeRequest
{
    /**
     * @var array
     */
    protected $info;

    public function __construct($info, array $headers = [])
    {
        $this->info = $info;
        parent::__construct('auth', $headers);
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param array $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }

    public function getBody()
    {
        return json_encode($this->info);
    }
}