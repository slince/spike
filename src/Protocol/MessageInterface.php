<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

interface MessageInterface
{
    /**
     * Creates a protocol from a string
     * @param $string
     * @return MessageInterface
     */
    public static function fromString($string);

    /**
     * Convert the protocol to string
     * @return string
     */
    public function toString();
}