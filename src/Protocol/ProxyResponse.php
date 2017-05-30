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

    public function __construct($result, ResponseInterface $response = null)
    {
        $this->response = $response;
        parent::__construct($result, $response);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    protected function parseToString(ResponseInterface $response)
    {
        return serialize($response);
    }
}