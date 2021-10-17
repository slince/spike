<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use React\EventLoop\LoopInterface;
use React\Stream\WritableResourceStream;
use React\Stream\WritableStreamInterface;

class AsyncStreamHandler extends StreamHandler
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
     * Sets loop instance.
     *
     * @param LoopInterface $loop
     */
    public function setLoop(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * @inheritdoc
     */
    protected function streamWrite($stream, array $record): void
    {
        if ($this->fallback) {
            parent::streamWrite($stream, $record);
            return;
        }
        try {
            $this->getAsyncStream()->write((string) $record['formatted']);
        } catch (\RuntimeException $exception) {
            //Polyfill
            $this->fallback = true;
            parent::streamWrite($stream, $record);
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