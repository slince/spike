<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use Monolog\Logger;
use Slince\Config\Config;
use Spike\Exception\InvalidArgumentException;

class Configuration  extends Config
{
    public function getServerAddress()
    {
        $address = $this->get('server-address');
        if (!$address) {
            throw new InvalidArgumentException("You should provide a server address");
        }
        return $address;
    }

    public function getAddress()
    {
        $address = $this->get('address');
        if (!$address) {
            throw new InvalidArgumentException("You should provide a valid address");
        }
        return $address;
    }

    /**
     * Gets the current timezone
     * @return string
     */
    public function getTimezone()
    {
        return $this->get('timezone', 'Asia/shanghai');
    }

    public function getLogFile()
    {
        return $this->get('log.file', getcwd() . '/access.log');
    }

    public function getLogLevel()
    {
        return $this->get('log.level', Logger::INFO);
    }

    public function getDefaultServerConfigFile()
    {
        return getcwd() . '/' . 'spike-server.json';
    }

    public function getDefaultClientConfigFile()
    {
        return getcwd() . '/' . 'spike.json';
    }
}