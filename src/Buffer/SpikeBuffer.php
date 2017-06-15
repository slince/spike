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
        //Checks whether the message is valid spike protocol
        if (empty($this->content) && stripos($data, 'spike') === false) {
            throw new InvalidArgumentException('Bad spike message');
        }
        $this->content .= $data;
        $pos = strpos($this->content, "\r\n\r\n");
        if ($pos !== false) {
            $this->headers = substr($this->content, 0, $pos);
            $this->body = substr($this->content, $pos + 4);
            if (preg_match("/Content-Length: ?(\d+)/i", $this->headers, $match)) {
                $bodyLength = $match[1];
                $furtherBodyLength = $bodyLength - strlen($this->body);

                if ($furtherBodyLength > 0) {
                    $this->connection->removeListener('data', [$this, 'handleData']);
                    $bodyBuffer = new FixedLengthBuffer($this->connection, $furtherBodyLength);
                    $bodyBuffer->gather(function(BufferInterface $bodyBuffer){
                        $this->body .= $bodyBuffer->getMessage();
                        $this->content .= $bodyBuffer->getContent();
                        $bodyBuffer->destroy();
                        unset($bodyBuffer);
                        $this->gatherComplete();
                        $this->connection->on('data', [$this, 'handleData']);
                    });
                } else {
                    $this->body = substr($this->body, 0, $bodyLength);
                    $this->gatherComplete();
                }
            } else {
                throw new InvalidArgumentException('Bad spike message');
            }
        }
    }

    protected function gatherComplete()
    {
        $this->message = $this->headers . "\r\n\r\n" . $this->body;
        parent::gatherComplete();
        if (strpos($this->content, "\r\n\r\n") !== false) {
            $this->handleData('');
        }
    }
}