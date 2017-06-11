<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

class RegisterTunnelResponse extends SpikeResponse
{
    /**
     * @var array
     */
    protected $body;

    public function __construct($code, $body, array $headers = [])
    {
        $this->body = $body;
        parent::__construct($code,'register_tunnel_response', $headers);
    }

    public function getBody()
    {
        return $this->body;
    }

    public static function parseBody($body)
    {
        return $body;
    }
}