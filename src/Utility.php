<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use Slince\Di\Container;

final class Utility
{
    /**
     * @var Container
     */
    protected static $container;

    public static function getContainer()
    {
        if (!is_null(static::$container)) {
            return static::$container;
        }
        return static::$container  = new Container();
    }
}