<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Spike\Server\Exception\BadRequestException;

abstract class Request extends Message implements RequestInterface
{
    public function toString()
    {
        $headers = array_merge([
            'Spike-Action' => $this->action,
            'Spike-Version' => MessageInterface::VERSION,
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
        list($headers, $bodyBuffer) = Message::parseMessages($string);
        if (!isset($headers['action'])) {
            throw new BadRequestException('Missing value for the header "action"');
        }
        $bodyBuffer = trim($bodyBuffer);
        return new static(static::parseBody($bodyBuffer), $headers);
    }
}