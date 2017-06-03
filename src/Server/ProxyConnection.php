<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use React\Socket\ConnectionInterface;
use Spike\Protocol\ProxyRequest;

class ProxyConnection
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var ProxyRequest
     */
    protected $proxyRequest;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param ProxyRequest $proxyRequest
     */
    public function setProxyRequest($proxyRequest)
    {
        $this->proxyRequest = $proxyRequest;
    }

    /**
     * @return ProxyRequest
     */
    public function getProxyRequest()
    {
        return $this->proxyRequest;
    }

    /**
     * Gets the connection id
     * @return string
     */
    public function getId()
    {
        return spl_object_hash($this->connection);
    }
}