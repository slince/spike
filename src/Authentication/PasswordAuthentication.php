<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Authentication;

use Spike\Exception\InvalidArgumentException;

class PasswordAuthentication implements AuthenticationInterface
{
    protected $auth;

    public function __construct($auth)
    {
        $this->auth = $auth;
    }

    public function verify($auth)
    {
        if (!isset($auth['username'])) {
            throw new InvalidArgumentException("Invalid arguments");
        }
        return ($this->auth['username']  == $auth['username'])
            && (!isset($this->auth['password']) || $this->auth['password'] == $auth['password']);
    }
}