<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Psr\Http\Message\ResponseInterface;

class ProxyResponse extends Response
{
    /**
     * @var Response
     */
    protected $response;

    public function __construct($code, ResponseInterface $response = null)
    {
        $this->response = $response;
        parent::__construct($code, 'proxy_response');
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function getBody()
    {
        return serialize($this->response);
    }

    public function parseBody($body)
    {
        $this->response = unserialize($body);
    }
}