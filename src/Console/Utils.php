<?php

namespace Spike\Console;

use React\Stream\WritableResourceStream;
use Spike\Log\AsyncStreamHandler;

final class Utils
{
    /**
     * Create file handler.
     *
     * @param string $file
     * @param int|string $level
     * @return AsyncStreamHandler
     */
    public static function createLogFileHandler(string $file, $level): AsyncStreamHandler
    {
        $resource = fopen($file, 'a+');
        return Utils::createLogHandler($resource, $level);
    }

    /**
     * Create log handler.
     *
     * @param resource $resource
     * @param int|string $level
     * @return AsyncStreamHandler
     */
    public static function createLogHandler($resource, $level): AsyncStreamHandler
    {
        $stream = new WritableResourceStream($resource);
        return new AsyncStreamHandler($stream, $level);
    }
}