<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

interface ProtocolInterface
{
    /**
     * Creates a protocol from a string
     * @param $string
     * @return ProtocolInterface
     */
    public static function fromString($string);

    /**
     * Convert the protocol to string
     * @return string
     */
    public function toString();
}