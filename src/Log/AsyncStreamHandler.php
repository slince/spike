<?php


namespace Spike\Log;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use React\Stream\WritableStreamInterface;

class AsyncStreamHandler extends AbstractProcessingHandler
{
    /**
     * @var WritableStreamInterface
     */
    protected $stream;

    public function __construct($stream, $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->stream = $stream;
        parent::__construct($level, $bubble);
    }

    /**
     * @inheritDoc
     */
    protected function write(array $record): void
    {
        $this->stream->write((string) $record['formatted']);
    }
}