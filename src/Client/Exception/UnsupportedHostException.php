<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Exception;

use Spike\Exception\RuntimeException;

class UnsupportedHostException extends RuntimeException
{
    public function __construct($message = '')
    {
        $message = $message ?: 'The host is not supported by the client';
        parent::__construct($message);
    }
}