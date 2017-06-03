<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Psr\Http\Message\RequestInterface as Psr7Request;

/**
 * Version: 1.0
 * Action: proxy_request
 * raw_request_message
 */
class ProxyRequest extends SpikeRequest
{
    /**
     * @var Psr7Request
     */
    protected $request;

    public function __construct(Psr7Request $request, $headers = [])
    {
        $this->request = $request;
        parent::__construct('proxy_request', $headers);
    }

    /**
     * @return Psr7Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function getBody()
    {
        return serialize($this->request);
    }

    public static function parseBody($body)
    {
        return unserialize($body);
    }
}