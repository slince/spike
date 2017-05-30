<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Psr\Http\Message\RequestInterface;

/**
 * Version: 1.0
 * Action: proxy_request
 *
 *
 * raw_request_message
 */
class ProxyRequest extends Request
{
    /**
     * @var RequestInterface
     */
    protected $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        parent::__construct('proxy_request');
    }

    public function getBody()
    {
        return \GuzzleHttp\json_encode($this->request);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    public static function fromString($string)
    {
    }
}