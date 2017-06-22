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
        $address = $this->get('address');
        if (!$address) {
            throw new InvalidArgumentException("You should provide a valid address");
        }
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
     * @return AuthenticationInterface
     */
    public function getAuthentication()
    {
        $type = isset($this['auth']['type']) ? $this['auth']['type'] : 'simple_password';
        switch ($type) {
            default:
                $authentication = new Authentication\PasswordAuthentication($this['auth']);
                break;
        }
        return $authentication;
    }
}