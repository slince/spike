<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Buffer;

use Spike\Exception\InvalidArgumentException;

class HttpHeaderBuffer extends HeaderBuffer
{
    public function handleData($data)
    {
        //Checks whether the message is valid spike protocol
        if (empty($this->headers) && stripos($data, 'http') === false) {
            throw new InvalidArgumentException('Bad http message');
        }
        parent::handleData($data);
    }
}