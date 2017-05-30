<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

interface ProtocolInterface
{
    /**
     * @param $string
     * @return ProtocolInterface
     */
    public static function fromString($string);
}