<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

class Factory
{
    const TYPE_DOMAIN_REGISTRY = 0;

    public static function create($buffer)
    {
        $flag = is_resource($buffer) ?
            fgets($buffer) : explode("r\n", $buffer);
        switch ($flag) {
            case static::TYPE_DOMAIN_REGISTRY:
                $protocol = DomainRegisterRequest::fromString($buffer);
                break;
            default:
                $protocol = HttpRequest::fromString($buffer);
        }
        return $protocol;
    }
}