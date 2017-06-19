<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use React\Socket\ConnectionInterface;
use Spike\Parser\HttpHeaderParser;

class HttpTunnelServer extends TunnelServer
{
    public function handleProxyConnection(ProxyConnection $proxyConnection)
    {
        $parser = new HttpHeaderParser();
        $proxyConnection->getConnection()->on('data', function($data) use ($parser, $proxyConnection){
            $parser->pushIncoming($data);
            $message = $parser->parseFirst();
            echo $message;
            if ($message) {
                $psrRequest = Psr7\parse_request($message);
                $host = $psrRequest->getUri()->getHost();
                if ($this->tunnel->supportProxyHost($host)) {
                    $this->tunnel->setProxyHost($host);
                    $httpMessage = $message . $parser->getRestData();
                    $proxyConnection->setInitBuffer($httpMessage);
                    parent::handleProxyConnection($proxyConnection);
                } else {
                    $body = sprintf('The host "%s" was not bound.', $host);
                    $response = $this->makeErrorResponse(404, $body);
                    $proxyConnection->getConnection()->end(Psr7\str($response));
                }
            }
        });
    }

    protected function makeErrorResponse($code, $message)
    {
        $message = $message ?: 'Proxy error';
        return new Response($code, [
            'Content-Length' => strlen($message)
        ], $message);
    }
}