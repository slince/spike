<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server;

use Slince\Config\Config;
use Spike\Common\Authentication\AuthenticationInterface;
use Spike\Common\Authentication;

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

    /**
     * Gets the server address to bind
     * @return string
     */
    public function getAddress()
    {
        $address = $this->get('address', '127.0.0.1:8090');
        return $address;
    }

    /**
     * Gets the config file
     * @return string
     */
    public function getDefaultConfigFile()
    {
        return getcwd() . '/' . 'spiked.json';
    }

    /**
     * Gets the authentication
     * @return AuthenticationInterface|null
     */
    public function getAuthentication()
    {
        $auth = $this->get('auth', []);
        $type = isset($auth['type']) ? $auth['type'] : 'simple_password';
        unset($auth['type']);
        if ($auth) {
            switch ($type) {
                default:
                    $authentication = new Authentication\PasswordAuthentication($auth);
                    break;
            }
        } else {
            $authentication = null;
        }
        return $authentication;
    }
}