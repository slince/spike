<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Buffer;

use React\Socket\ConnectionInterface;
use Spike\Exception\InvalidArgumentException;

class SpikeBuffer extends Buffer
{
    protected $headers;

    protected $body;

    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);
        $this->connection->on('data', [$this, 'handleData']);
    }

    public function handleData($data)
    {
        $this->headers .= $data;
        $pos = strpos($this->headers, "\r\n\r\n");
        if ($pos !== false) {
            $this->headers = substr($this->headers, 0, $pos);
            $this->body .= substr($this->headers, $pos + 4);

            $this->connection->removeListener('data', [$this, 'handleData']);

            if (preg_match("/Content-Length: ?(\d+)/i", $this->headers, $match)) {
                $length = $match[1];
                $bodyBuffer = new LengthLimitBuffer($this->connection, $length - strlen($this->body));
            } else {
                throw new InvalidArgumentException('Bad http message');
            }
            $bodyBuffer->gather(function(BufferInterface $bodyBuffer){
                $this->body .= (string)$bodyBuffer;
                $this->handleComplete();
            });
        }
    }

    protected function handleComplete()
    {
        $this->content = $this->headers . "\r\n\r\n" . $this->body;
        $this->isGatherComplete = true;
        call_user_func($this->callback, $this);
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        parent::flush();
        $this->connection->on('data', [$this, 'handleData']);
    }
}