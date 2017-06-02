<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

abstract class Message implements MessageInterface
{
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

    public function __construct($action, $headers = [])
    {
        $this->action = $action;
        $this->headers = $headers;
    }

    public function __toString()
    {
        return $this->toString();
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

    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * Parses the message
     * @param string $message
     * @return array
     */
    public static function parseMessages($message)
    {
        list($headerBuffer, $bodyBuffer) = explode("\r\n\r\n", $message, 2);
        $lines = preg_split('/(\\r?\\n)/', $headerBuffer, -1, PREG_SPLIT_DELIM_CAPTURE);
        $headers = [];
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            $header = trim($parts[0]);
            $headers[$header] = isset($parts[1]) ? trim($parts[1]) : null;
        }
        return [$headers, trim($bodyBuffer)];
    }
}