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