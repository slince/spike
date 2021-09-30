<?php

namespace Spike\Connection;

use React\Stream\DuplexStreamInterface;
use Spike\Command\CommandInterface;
use Spike\Protocol\Message;
use Spike\Protocol\MessageParser;

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

    public function listenRaw(callable $callback)
    {
        $this->stream->on('data', $callback);
    }

    public function listen(callable $callback)
    {
        $parser = new MessageParser($this);
        $parser->on('message', $callback);
        $parser->parse();
    }
}