<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

use GuzzleHttp\Psr7;
use Spike\Server\Exception\BadRequestException;

class Factory
{
    /**
     * @param $buffer
     * @return Psr7\Request|DomainRegisterRequest|ProtocolInterface
     */
    public static function create($buffer)
    {
        $firstLine = is_resource($buffer) ?
            fgets($buffer) : explode("\r\n", $buffer);
        $isHttpRequest = strpos($firstLine, 'HTTP') !== false;
        if ($isHttpRequest) {
            $protocol = Psr7\parse_request($buffer);
        } else {
            list(, $flag) = explode(':', $firstLine);
            switch ($flag) {
                case 'register_domain':
                    $protocol = DomainRegisterRequest::fromString($buffer);
                    break;
                case 'register_domain_response':
                    $protocol = DomainRegisterResponse::fromString($buffer);
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