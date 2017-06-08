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
                case 'proxy_request':
                    $protocol = ProxyRequest::fromString($buffer);
                    break;
                case 'proxy_response':
                    $protocol = ProxyResponse::fromString($buffer);
                    break;
                default:
                    throw new BadRequestException('Bad request');
            }
        }
        return $protocol;
    }
}