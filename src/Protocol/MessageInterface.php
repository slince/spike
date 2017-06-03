<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

interface MessageInterface
{
    /**
     * The version of protocol
     * @var string
     */
    const VERSION = 1.0;

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

    /**
     * Gets the message header by given name
     * @param string $name
     * @return string
     */
    public function getHeader($name);
}