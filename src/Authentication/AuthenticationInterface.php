<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Authentication;

interface AuthenticationInterface
{
    /**
     * Verify the auth information
     * @param mixed $auth
     * @return boolean
     */
    public function verify($auth);
}