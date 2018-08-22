<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server;

use React\Socket\ConnectionInterface;
use Spike\Client\ClientInterface;

class Client implements ClientInterface
{
    /**
     * id.
     *
     * @var string
     */
    protected $id;

    /**
     * Client information.
     *
     * @var array
     */
    protected $info;

    /**
     * @var \DateTimeInterface
     */
    protected $activeAt;

    /**
     * @var ConnectionInterface
     */
    protected $controlConnection;

    public function __construct($info, ConnectionInterface $controlConnection)
    {
        $this->info = $info;
        $this->controlConnection = $controlConnection;
        $this->activeAt = new \DateTime();
    }

    /**
     * Sets the control connection for the client.
     *
     * @param ConnectionInterface $controlConnection
     */
    public function setControlConnection($controlConnection)
    {
        $this->controlConnection = $controlConnection;
    }

    /**
     * Gets the control connection of the client.
     *
     * @return ConnectionInterface
     */
    public function getControlConnection()
    {
        return $this->controlConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id ?: ($this->id = spl_object_hash($this));
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function setActiveAt($activeAt)
    {
        $this->activeAt = $activeAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveAt()
    {
        return $this->activeAt;
    }

    /**
     * Gets the client information.
     *
     * @return array
     */
    public function toArray()
    {
        return array_replace($this->info, [
            'id' => $this->getId(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
    }

    /**
     * Close the client.
     */
    public function close()
    {
        if ($this->controlConnection) {
            $this->controlConnection->removeAllListeners('close');
            $this->controlConnection->end();
        }
    }
}