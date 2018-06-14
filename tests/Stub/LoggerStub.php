<?php
namespace Spike\Tests\Stub;

use Spike\Common\Logger\Logger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

class LoggerStub extends Logger
{
    public function __construct($loop, $level)
    {
        $file = tempnam(sys_get_temp_dir(), 'tmp_');
        $stream = fopen('php://memory', 'a+');
        $output = new StreamOutput($stream);
        parent::__construct($loop, $level, $file, $output);
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }
}