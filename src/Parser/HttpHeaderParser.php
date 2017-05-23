<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Parser;

class HttpHeaderParser extends AbstractParser
{
    /**
     * Parse the incoming buffer
     * @return array
     */
    public function parse()
    {
        $messages = [];
        while ($this->incomingData) {
            $message = $this->parseFirst();
            if (is_null($message)) {
                break;
            }
            $messages[] = $message;
        }
        return $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function parseFirst()
    {
        $pos = strpos($this->incomingData, "\r\n\r\n");
        if ($pos === false) {
            return null;
        }
        $message = substr($this->incomingData, 0, $pos + 4);
        $this->incomingData  = substr($this->incomingData, strlen($message));
        return $message;
    }
}