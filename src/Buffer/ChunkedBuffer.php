<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Buffer;

use React\Socket\ConnectionInterface;
use Spike\Exception\InvalidArgumentException;

class ChunkedBuffer extends Buffer
{
    const CHUNK_SIZE = 1024;

    const CRLF = "\r\n";

    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);
        $this->connection->on('data', function($data){
            $this->handleData($data);
        });
    }

    protected function handleData($data)
    {
        if (($pos = strpos($data, static::CRLF)) !== false) {
            $lengthHex = substr($data, 0, $pos);
        } else {
            throw new InvalidArgumentException("Bad message chunk");
        }
        $length = hexdec($lengthHex);
        if ($length == 0) {
            $this->isGatherComplete = true;
            call_user_func($this->callback, $this);
            return;
        }
        $this->content .= substr($data, $pos + 2, $length);
    }
}