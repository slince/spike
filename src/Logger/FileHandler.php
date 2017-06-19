<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Logger;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class FileHandler extends StreamHandler
{
    public function __construct($file, $level)
    {
        parent::__construct($file, $level, true, null, false);
        $this->setFormatter(new LineFormatter("[%datetime%] %level_name%: %message%\n"));
    }
}