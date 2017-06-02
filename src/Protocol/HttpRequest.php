<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface as Psr7Request;

class HttpRequest implements MessageInterface
{
    /**
     * @var Psr7Request
     */
    protected $request;

    public function __construct(Psr7Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Psr7Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Creates a protocol from a string
     * @param $string
     * @return HttpRequest
     */
    public static function fromString($string)
    {
        return new static(Psr7\parse_request($string));
    }

    /**
     * Convert the protocol to string
     * @return string
     */
    public function toString()
    {
        return Psr7\str($this->request);
    }
}