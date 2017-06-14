<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use React\Socket\ConnectionInterface;

class Client
{
    protected $id;

    protected $clientInfo;

    /**
     * @var ConnectionInterface
     */
    protected $controlConnection;

    public function __construct($clientInfo, ConnectionInterface $controlConnection)
    {
        $this->clientInfo = $clientInfo;
        $this->controlConnection = $controlConnection;
    }

    /**
     * @param ConnectionInterface $controlConnection
     */
    public function setControlConnection($controlConnection)
    {
        $this->controlConnection = $controlConnection;
    }

    /**
     * @return ConnectionInterface
     */
    public function getControlConnection()
    {
        return $this->controlConnection;
    }

    public function getId()
    {
        return $this->id ?: ($this->id = spl_object_hash($this));
    }

    /**
     * @return mixed
     */
    public function getClientInfo()
    {
        return $this->clientInfo;
    }

    public function toArray()
    {
        return $this->getClientInfo() + [
            'id' => $this->getId()
        ];
    }
}