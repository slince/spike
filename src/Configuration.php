<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use Slince\Config\Config;

class Configuration  extends Config
{
    public function getServerAddress()
    {
        return $this->get('server.address', '127.0.0.1:80');
    }
}