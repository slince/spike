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