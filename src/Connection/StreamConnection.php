<?php

namespace Spike\Connection;

use React\Stream\DuplexStreamInterface;
use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class StreamConnection implements ConnectionInterface
{
    /**
     * @var DuplexStreamInterface
     */
    protected $stream;

    public function __construct(DuplexStreamInterface $stream)
    {
        $this->stream = $stream;
    }

    public function connect()
    {

    }

    public function disconnect()
    {
        $this->stream->close();
    }


    public function writeRequest(CommandInterface $command)
    {
        $message = Message::pack($command->createMessage());
        $this->stream->write($message);
    }

    public function executeCommand(CommandInterface $command)
    {
        $this->writeRequest($command);
    }
}