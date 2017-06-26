<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use Spike\Exception\InvalidArgumentException;

class Utility
{
    /**
     * Parse the address ("127..0.0.1:80" => ['127.0.0.1', 80])
     * @param string $address
     * @return array
     */
    public static function parseAddress($address)
    {
        $parts = array_map('trim', array_slice(explode(':', $address), 0, 2));
        if (count($parts) !== 2) {
            throw new InvalidArgumentException(sprintf('The address "%s" is invalid', $address));
        }
        return $parts;
    }
}