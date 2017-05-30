<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface;

class HttpRequest
{
    /**
     * @var RequestInterface
     */
    protected $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public static function fromString($string)
    {
        $request = Psr7\parse_request($string);
        return new HttpRequest($request);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function getUid()
    {
        return spl_object_hash($this);
    }
}