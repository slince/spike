<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol\Parser;

use Spike\Exception\InvalidArgumentException;

class SpikeParser
{
    /**
     * The incoming buffer
     * @var string
     */
    protected $incomingBuffer;

    public function pushIncoming($chunkBuffer)
    {
        $this->incomingBuffer .= $chunkBuffer;
        return $this;
    }

    /**
     * Parse the incoming buffer
     * @return array
     */
    public function parse()
    {
        $messages = [];
        while ($this->incomingBuffer) {
            $message = $this->parseToMessage();
            if (is_null($message)) {
                break;
            }
            $messages[] = $message;
        }
        return $messages;
    }

    protected function parseToMessage()
    {
        $pos = strpos($this->incomingBuffer, "\r\n\r\n");
        if ($pos === false) {
            return null;
        }
        $header = substr($this->incomingBuffer, 0, $pos);
        if (preg_match("/Content-Length: ?(\d+)/i", $header, $match)) {
            $bodyLength = $match[1];
            //incoming buffer length - header length  - two\r\n
            if (strlen($this->incomingBuffer) - $pos - 4 >= $bodyLength) {
                $body = substr($this->incomingBuffer, $pos + 4, $bodyLength);
            }  else {
                return null;
            }
            $message = $header . "\r\n\r\n" . $body;
            $this->incomingBuffer  = substr($this->incomingBuffer, strlen($message));
        } else {
            throw new InvalidArgumentException('Bad spike message');
        }
        return $message;
    }
}