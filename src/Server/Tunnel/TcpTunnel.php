<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Tunnel;

use React\Socket\ConnectionInterface;

class TcpTunnel extends Tunnel
{
    /**
     * @var array
     */
    protected $host;

    public function __construct($port, $host, ConnectionInterface $connection = null)
    {
        $this->host = $host;
        parent::__construct($port, $connection);
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }
}