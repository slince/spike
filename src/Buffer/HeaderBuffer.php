<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Buffer;

use React\Socket\ConnectionInterface;

class HeaderBuffer extends Buffer
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);
        $this->connection->on('data', [$this, 'handleData']);
    }

    public function handleData($data)
    {
        $this->content .= $data;
        $pos = strpos($this->content, "\r\n\r\n");
        if ($pos !== false) {
            $this->content .= substr($this->content, 0, $pos);
            $this->gatherComplete();
        }
    }
}