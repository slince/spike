<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Authentication;

class PasswordAuthentication implements AuthenticationInterface
{
    protected $auth;

    public function __construct($auth)
    {
        $this->auth = $auth;
    }

    public function verify($auth)
    {
        return ($this->auth['username']  == $auth['username'])
            && (!isset($this->auth['password']) || $this->auth['password'] == $auth['username']);
    }
}