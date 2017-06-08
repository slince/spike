<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use Spike\Buffer\BufferInterface;
use Spike\Buffer\HttpBuffer;
use Spike\Exception\BadRequestException;

class ProtocolFactory
{
    /**
     * @param BufferInterface $buffer
     * @return MessageInterface
     */
    public static function create($buffer)
    {
        if ($buffer instanceof HttpBuffer) {
            $protocol = HttpRequest::fromString($buffer);
        } else {
            list(, $flag) = explode(':', strstr($buffer, "\r\n", true));
            $flag = trim($flag);
            switch ($flag) {
                case 'register_tunnel':
                    $protocol = RegisterTunnel::fromString($buffer);
                    break;
                default:
                    throw new BadRequestException(sprintf('Bad request: "%s"', $buffer));
            }
        }
        return $protocol;
    }
}