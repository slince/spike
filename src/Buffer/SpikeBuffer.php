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
        $this->connection->once('data', [$this, 'handleData']);
    }

    public function handleData($data)
    {
        //Checks whether the message is valid spike protocol
        if (empty($this->headers) && stripos($data, 'spike') === false) {
            throw new InvalidArgumentException('Bad spike message');
        }
        $this->headers .= $data;
        $pos = strpos($this->headers, "\r\n\r\n");
        if ($pos !== false) {
            $this->body = substr($this->headers, $pos + 4);
            $this->headers = substr($this->headers, 0, $pos);
            if (preg_match("/Content-Length: ?(\d+)/i", $this->headers, $match)) {
                $length = $match[1];
                $furtherContentLength = $length - strlen($this->body);
                if ($furtherContentLength > 0) {
                    $this->connection->removeListener('data', [$this, 'handleData']);
                    $bodyBuffer = new FixedLengthBuffer($this->connection, $furtherContentLength);
                    $bodyBuffer->gather(function(BufferInterface $bodyBuffer){
                        $this->body .= (string)$bodyBuffer;
                        $this->gatherComplete();
//                        $bodyBuffer->destroy();
                    });
                } else {
                    $this->gatherComplete();
                }
            } else {
                throw new InvalidArgumentException('Bad spike message');
            }
        }
    }

    protected function gatherComplete()
    {
        $this->content = $this->headers . "\r\n\r\n" . $this->body;
        parent::gatherComplete();
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