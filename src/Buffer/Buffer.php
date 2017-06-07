<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Buffer;

use React\Socket\ConnectionInterface;

abstract class Buffer implements BufferInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * The buffer content
     * @var string
     */
    protected $content;

    /**
     * Checks whether gathers ok
     * @var boolean
     */
    protected $isGatherComplete;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    public function getContent()
    {
        return (string)$this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function gather(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function isGatherComplete()
    {
        return $this->isGatherComplete;
    }

    /**
     * Gathers ok
     */
    protected function gatherComplete()
    {
        $this->isGatherComplete = true;
        call_user_func($this->callback, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->content = '';
        $this->isGatherComplete = false;
    }
}