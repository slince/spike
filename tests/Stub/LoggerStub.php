<?php
namespace Spike\Tests\Stub;

use Spike\Common\Logger\Logger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class LoggerStub extends Logger
{
    protected $file;

    public function __construct($loop, $level)
    {
        $file = tempnam(sys_get_temp_dir(), 'tmp_');
        $stream = fopen('php://memory', 'a+');
        $output = new StreamOutput($stream);
        $this->file = $file;
        parent::__construct($loop, $level, $file, $output);
    }

    /**
     * @return bool|string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }
}