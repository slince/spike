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
     * The value will be always true on ms window.
     *
     * @var bool
     */
    protected $fallback = false;

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
        if ($this->fallback) {
            parent::doWrite($message, $newline);
            return;
        }

        if ($newline) {
            $message .= \PHP_EOL;
        }

        try {
            $this->getAsyncStream()->write($message);
        } catch (\RuntimeException $exception) {
            //Polyfill
            $this->fallback = true;
            parent::doWrite($message, false);
        }
    }

    protected function getAsyncStream()
    {
        if (null !== $this->writableStream) {
            return $this->writableStream;
        }
        return $this->writableStream = new WritableResourceStream($this->getStream(), $this->loop);
    }
}