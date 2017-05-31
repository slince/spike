<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Psr\Http\Message\ResponseInterface as Psr7Response;

class ProxyResponse extends Response
{
    /**
     * @var Psr7Response
     */
    protected $response;

    public function __construct($code, Psr7Response $response = null)
    {
        $this->response = $response;
        parent::__construct($code, 'proxy_response');
    }

    /**
     * @return Psr7Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function getBody()
    {
        return serialize($this->response);
    }

    public static function parseBody($body)
    {
        return unserialize($body);
    }
}