<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use React\Socket\ConnectionInterface;

class ChunkBuffer
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The buffer read from the connection
     * @var string
     */
    protected $buffer;

    /**
     * @var callable
     */
    protected $callback;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->connection->on('data', function($data){
            $this->buffer = $data;
        });
        $this->connection->on('end', function(){
            call_user_func($this->callback, $this->buffer);
        });
    }

    /**
     * Waiting gather buffer
     * @param callable $callback
     * @return $this
     */
    public function gather(callable  $callback)
    {
        $this->callback = $callback;
        return $this;
    }
}