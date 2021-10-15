<?php

declare(strict_types=1);

namespace Spike\Process;

use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;

abstract class AbstractProcess implements ProcessInterface
{
    /**
     * @var WritableStreamInterface
     */
    public $stdin;

    /**
     * @var ReadableStreamInterface
     */
    public $stdout;

    /**
     * @var ReadableStreamInterface
     */
    public $stderr;

    /**
     * {@inheritdoc}
     */
    public function getStdin(): WritableStreamInterface
    {
        return $this->stdin;
    }

    /**
     * {@inheritdoc}
     */
    public function getStdout(): ReadableStreamInterface
    {
        return $this->stdout;
    }

    /**
     * {@inheritdoc}
     */
    public function getStderr(): ReadableStreamInterface
    {
        return $this->stderr;
    }
}