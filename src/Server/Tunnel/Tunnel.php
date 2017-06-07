<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Tunnel;

use React\Socket\ConnectionInterface;

class Tunnel
{
    /**
     * The supported protocol of the tunnel
     * @var string
     */
    protected $protocol;

    /**
     * The remote port
     * @var int
     */
    protected $remotePort;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function __construct(ConnectionInterface $connection, $protocol, $remotePort)
    {
        $this->connection = $connection;
        $this->protocol = $protocol;
        $this->remotePort = $remotePort;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return int
     */
    public function getRemotePort()
    {
        return $this->remotePort;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }
}