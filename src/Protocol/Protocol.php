<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Spike\Server\Exception\BadRequestException;

abstract class Protocol implements ProtocolInterface
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

    public function __construct($action, $headers = [])
    {
        $this->action = $action;
        $this->headers = $headers;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        $headers = array_merge([
            'Spike-Action' => $this->action,
            'Spike-Version' => static::VERSION,
        ], $this->headers);
        $buffer = '';
        foreach ($headers as $header) {
            $buffer .= ": {$header}\r\n";
        }
        return $buffer
            . "\r\n\r\n"
            . $this->getBody();
    }

    public static function fromString($string)
    {
        list($headerBuffer, $bodyBuffer) = explode("\r\n", $string, 2);
        $lines = preg_split('/(\\r?\\n)/', $headerBuffer, -1, PREG_SPLIT_DELIM_CAPTURE);
        $headers = [];
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            $header = trim($parts[0]);
            $headers[$header] = isset($parts[1]) ? trim($parts[1]) : null;
        }
        if (!isset($headers['action'])) {
            throw new BadRequestException('Missing value for the header "action"');
        }
        $protocol = new static($headers['action'], $headers);
        $bodyBuffer && $protocol->parseBody($bodyBuffer);
        return $protocol;
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

    abstract public function getBody();

    abstract function parseBody($body);
}