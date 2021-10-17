<?php

namespace Spike\Console\Output;

use React\EventLoop\LoopInterface;
use React\Stream\WritableResourceStream;
use React\Stream\WritableStreamInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class AsyncConsoleOutput extends ConsoleOutput
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var WritableStreamInterface
     */
    protected $writableStream;

    /**
     * Set loop for
     * @param LoopInterface $loop
     */
    public function setLoop(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite(string $message, bool $newline)
    {
        if ($newline) {
            $message .= \PHP_EOL;
        }

        $this->getAsyncStream()->write($message);
    }

    protected function getAsyncStream()
    {
        if (null !== $this->writableStream) {
            return $this->writableStream;
        }
        return $this->writableStream = new WritableResourceStream($this->getStream(), $this->loop);
    }
}