<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Spike\Exception\BadRequestException;

abstract class Request extends Message implements RequestInterface
{
    public function toString()
    {
        $headers = array_merge([
            'Spike-Action' => $this->action,
            'Spike-Version' => MessageInterface::VERSION,
        ], $this->headers);
        $buffer = '';
        foreach ($headers as $header => $value) {
            $buffer .= "{$header}: {$value}\r\n";
        }
        return $buffer
            . "\r\n\r\n"
            . $this->getBody();
    }

    public static function fromString($string)
    {
        list($headers, $bodyBuffer) = Message::parseMessages($string);
        if (!isset($headers['Spike-Action'])) {
            throw new BadRequestException('Missing value for the header "action"');
        }
        $bodyBuffer = trim($bodyBuffer);
        return new static(static::parseBody($bodyBuffer), $headers);
    }
}