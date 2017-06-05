<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Exception;

use Spike\Exception\RuntimeException;

class UnsupportedHostException extends RuntimeException
{
    protected $connectionId;

    public function __construct($connectionId, $message = '')
    {
        $this->connectionId = $connectionId;
        $message = $message ?: 'The host is not supported by the client';
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getConnectionId()
    {
        return $this->connectionId;
    }
}