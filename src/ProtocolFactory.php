<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use GuzzleHttp\Psr7;
use Spike\Exception\BadRequestException;
use Spike\Protocol;

class ProtocolFactory
{
    /**
     * @param $buffer
     * @return Psr7\Request|Protocol\MessageInterface
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
            switch (trim($flag)) {
                case 'register_domain':
                    $protocol = Protocol\DomainRegisterRequest::fromString($buffer);
                    break;
                case 'register_domain_response':
                    $protocol = Protocol\DomainRegisterResponse::fromString($buffer);
                    break;
                case 'proxy_request':
                    $protocol = Protocol\ProxyRequest::fromString($buffer);
                    break;
                case 'proxy_response':
                    $protocol = Protocol\ProxyResponse::fromString($buffer);
                    break;
                default:
                    throw new BadRequestException('Bad request');
            }
        }
        return $protocol;
    }
}