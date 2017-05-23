<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Spike\Exception\BadRequestException;

class Spike implements SpikeInterface
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

    /**
     * @var mixed
     */
    protected $body;

    /**
     * The global headers
     * @var array
     */
    protected static $globalHeaders = [];

    /**
     * Spike constructor.
     * @param string $action
     * @param mixed $body
     * @param array $headers
     */
    public function __construct($action, $body = null, $headers = [])
    {
        $this->action = $action;
        $this->body = $body;
        $this->headers = $headers;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        $body = static::serializeBody($this->getBody());
        $headers = array_merge([
            'Spike-Action' => $this->action,
            'Spike-Version' => SpikeInterface::VERSION,
            'Content-Length' => strlen($body)
        ], $this->headers, static::$globalHeaders);
        $buffer = '';
        foreach ($headers as $header => $value) {
            $buffer .= "{$header}: {$value}\r\n";
        }
        return $buffer
            . "\r\n"
            . $body;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public static function serializeBody($body)
    {
        return json_encode($body);
    }

    /**
     * {@inheritdoc}
     */
    public static function unserializeBody($rawBody)
    {
        return json_decode($rawBody, true);
    }


    /**
     * {@inheritdoc}
     */
    public static function fromString($string)
    {
        list($headers, $bodyBuffer) = Spike::parseMessages($string);
        if (!isset($headers['Spike-Action'])) {
            throw new BadRequestException('Missing value for the header "action"');
        }
        $bodyBuffer = trim($bodyBuffer);
        return new static($headers['Spike-Action'], static::unserializeBody($bodyBuffer), $headers);
    }

    /**
     * Parses the message
     * @param string $message
     * @return array
     */
    protected static function parseMessages($message)
    {
        list($headerBuffer, $bodyBuffer) = explode("\r\n\r\n", $message, 2);
        $lines = preg_split('/(\\r?\\n)/', $headerBuffer, -1, PREG_SPLIT_DELIM_CAPTURE);
        $headers = [];
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            $header = trim($parts[0]);
            if ($header) {
                $headers[$header] = isset($parts[1]) ? trim($parts[1]) : null;
            }
        }
        return [$headers, trim($bodyBuffer)];
    }

    /**
     * Sets a global header
     * @param string $name
     * @param string $value
     */
    public static function setGlobalHeader($name, $value)
    {
        static::$globalHeaders[$name] = $value;
    }
}