<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use React\Socket\ConnectionInterface;

class ProxyHost
{
    protected $host;

    protected $connection;

    public function __construct($host, $connection)
    {
        $this->host = $host;
        $this->connection = $connection;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }
}