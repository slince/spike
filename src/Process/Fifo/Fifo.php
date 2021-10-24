<?php

namespace Spike\Process\Fifo;

use Spike\Exception\InvalidArgumentException;
use Spike\Exception\RuntimeException;

final class Fifo
{
    protected $pathname;

    protected $mode;

    protected $permission;

    protected $stream;

    public function __construct($pathname, $mode, $permission = 0666)
    {
        if (($exists = file_exists($pathname)) && filetype($pathname) !== 'fifo') {
            throw new InvalidArgumentException("The file already exists, but is not a valid fifo file");
        }
        if (!$exists && !posix_mkfifo($pathname, $permission)) {
            throw new RuntimeException("Cannot create the fifo file");
        }
        $this->pathname = $pathname;
        $this->mode = $mode;
        $this->permission = $permission;
    }

    public function getStream()
    {
        if (!is_null($this->stream)) {
            return $this->stream;
        }
        return $this->stream = fopen($this->pathname, $this->mode);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        is_resource($this->stream) && fclose($this->stream);
        @unlink($this->pathname);
    }
}