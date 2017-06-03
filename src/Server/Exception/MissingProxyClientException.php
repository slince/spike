<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Exception;

use Spike\Exception\RuntimeException;

class MissingProxyClientException extends RuntimeException
{
    public function __construct($message = '')
    {
        $message = $message ?: 'Cannot find the proxy client for the host';
        parent::__construct($message);
    }
}