<?php


namespace Spike\Server;

use React\Socket\ConnectionInterface;

final class Client
{
    /**
     * id.
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var \DateTimeInterface
     */
    protected $activeAt;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    public function __construct($id, ConnectionInterface $connection)
    {
        $this->id = $id;
        $this->connection = $connection;
        $this->createdAt = new \DateTime();
        $this->activeAt = new \DateTime();
    }

    /**
     * Sets the control connection for the client.
     * @param ConnectionInterface $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Gets the control connection of the client.
     *
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
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
     * Close the client.
     */
    public function close()
    {
        if ($this->connection) {
            $this->connection->removeAllListeners('close');
            $this->connection->end();
        }
    }
}