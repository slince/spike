<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use Spike\Authentication\AuthenticationInterface;
use Spike\Exception\InvalidArgumentException;
use Spike\Configuration as BaseConfiguration;
use Spike\Authentication;

class Configuration extends BaseConfiguration
{
    /**
     * Gets the server address to bind
     * @return string
     */
    public function getAddress()
    {
        $address = $this->get('address', '127.0.0.1:8088');
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