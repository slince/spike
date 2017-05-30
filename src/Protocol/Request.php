<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

abstract class Request implements ProtocolInterface
{
    /**
     * The version of protocol
     * @var string
     */
    const VERSION = 1.0;

    /**
     * The action
     * @var string
     */
    protected $action;

    /**
     * Array of custom headers
     * @var array
     */
    protected $headers = [];

    /**
     * Protocol body
     * @var string
     */
    protected $body;

    public function __construct($action, $body = null)
    {
        $this->action = $action;
        $this->body = $body;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        $headers = array_merge($this->headers, [
            'Version' => static::VERSION,
            'Action' => $this->action,
        ]);
        $buffer = '';
        foreach ($headers as $header) {
            $buffer .= $header . "\r\n";
        }
        return $buffer
            . "\r\n\r\n"
            . $this->getBody();
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }
}