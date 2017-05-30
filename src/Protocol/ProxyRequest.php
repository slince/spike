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
        parent::__construct('proxy_request', $this->parseToString($request));
    }

    protected function parseToString(RequestInterface $request)
    {
        return \GuzzleHttp\json_encode($request);
    }
}