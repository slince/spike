<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use Monolog\Logger;
use Slince\Config\Config;

class Configuration  extends Config
{
    /**
     * @return array
     */
    public function getTunnels()
    {
        return $this->get('tunnels', []);
    }

    /**
     * Gets the current timezone
     * @return string
     */
    public function getTimezone()
    {
        return $this->get('timezone', 'Asia/shanghai');
    }

    /**
     * Gets the log file
     * @return string
     */
    public function getLogFile()
    {
        return isset($this['log']['file']) ? $this['log']['file']: getcwd() . '/access.log';
    }

    /**
     * Gets the log level
     * @return int
     */
    public function getLogLevel()
    {
        return  isset($this['log']['level']) ? $this['log']['level']: 'info';
    }
}