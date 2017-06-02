<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Spike\Exception\BadResponseException;

abstract class Response extends Message implements ResponseInterface
{
    /**
     * The status code of the response
     * @var int
     */
    protected $code;

    public function __construct($code, $action, $headers = [])
    {
        $this->code = $code;
        parent::__construct($action, $headers);
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    public function toString()
    {
        $body = $this->getBody();
        $headers = array_merge([
            'Spike-Action' => $this->action,
            'Spike-Version' => static::VERSION,
            'Code' => $this->code,
            'Content-Length' => strlen($body)
        ], $this->headers);
        $buffer = '';
        foreach ($headers as $header => $value) {
            $buffer .= "{$header}: {$value}\r\n";
        }
        return $buffer
            . "\r\n\r\n"
            . $body;
    }

    public static function fromString($string)
    {
        list($headers, $bodyBuffer) = Message::parseMessages($string);
        if (!isset($headers['Spike-Action']) || !isset($headers['Code'])) {
            throw new BadResponseException('Missing value');
        }
        $bodyBuffer = trim($bodyBuffer);
        return new static(trim($headers['Code']), static::parseBody($bodyBuffer), $headers);
    }
}