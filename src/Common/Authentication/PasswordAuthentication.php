<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Common\Authentication;

use Spike\Common\Exception\InvalidArgumentException;

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
            throw new InvalidArgumentException('Invalid arguments');
        }

        return ($this->auth['username'] == $auth['username'])
            && (!isset($this->auth['password']) || $this->auth['password'] == $auth['password']);
    }
}