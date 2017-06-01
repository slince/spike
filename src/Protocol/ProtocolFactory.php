<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use GuzzleHttp\Psr7;
use Spike\Exception\BadRequestException;

class ProtocolFactory
{
    /**
     * @param $buffer
     * @return Psr7\Request|MessageInterface
     */
    public static function create($buffer)
    {
        $firstLine = is_resource($buffer) ?
            fgets($buffer) : explode("\r\n", $buffer)[0];
        $isHttpRequest = strpos($firstLine, 'HTTP') !== false;
        if ($isHttpRequest) {
            $protocol = Psr7\parse_request($buffer);
        } else {
            list(, $flag) = explode(':', $firstLine);
            $flag = trim($flag);
            switch ($flag) {
                case 'register_domain':
                    $protocol = RegisterHostRequest::fromString($buffer);
                    break;
                case 'register_domain_response':
                    $protocol = RegisterHostResponse::fromString($buffer);
                    break;
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