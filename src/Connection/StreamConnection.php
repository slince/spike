<?php

namespace Spike\Connection;

use React\Promise\PromiseInterface;
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

    /**
     * {@inheritdoc}
     */
    public function disconnect(bool $force = false)
    {
        $force ? $this->stream->close() : $this->stream->end();
    }

    /**
     * {@inheritdoc}
     */
    public function executeCommand(CommandInterface $command): PromiseInterface
    {
        $this->writeRequest($command);
    }

    public function writeRequest(CommandInterface $command)
    {
        $message = Message::pack($command->createMessage());
        $this->stream->write($message);
    }

    /**
     * {@inheritdoc}
     */
    public function listenRaw(callable $callback)
    {
        $this->stream->on('data', $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function listen(callable $callback)
    {
        $parser = new MessageParser($this);
        $parser->on('message', $callback);
        $parser->parse();
    }
}