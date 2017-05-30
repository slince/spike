<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

class Response implements ProtocolInterface
{
    const VERSION = 1.0;

    protected $code;

    protected $body;

    protected $headers;

    public function __construct($code, $body)
    {
        $this->code = $code;
        $this->body = $body;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        return "Version: " . static::VERSION . "\r\n"
            . "Code: {$this->code} \r\n"
            . "\r\n\r\n"
            . $this->body;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
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
    public function getHeaders()
    {
        return $this->headers;
    }

    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

}