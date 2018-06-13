<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Common\Protocol;

class HttpHeaderParser
{
    /**
     * The incoming buffer
     * @var string
     */
    protected $buffer;

    /**
     * Push incoming data to the parser
     * @param string $data
     * @return array
     */
    public function push($data)
    {
        $this->buffer .= $data;
        return $this->parse();
    }

    /**
     * Parse the incoming buffer
     * @return array
     */
    public function parse()
    {
        $messages = [];
        while ($this->buffer) {
            $message = $this->parseFirst();
            if (is_null($message)) {
                break;
            }
            $messages[] = $message;
        }
        return $messages;
    }

    /**
     * Parse one message from the data
     * @return string
     */
    public function parseFirst()
    {
        $pos = strpos($this->buffer, "\r\n\r\n");
        if ($pos === false) {
            return null;
        }
        $message = substr($this->buffer, 0, $pos + 4);
        $this->buffer = substr($this->buffer, strlen($message));
        return $message;
    }

    /**
     * Get the reset of data
     * @return string
     */
    public function getRemainingChunk()
    {
        return $this->buffer;
    }
}