<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use Spike\Parser\HttpHeaderParser;

class HttpTunnelServer extends TunnelServer
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * Make an error psr7 response
     * @param int $code
     * @param string $message
     * @return Response
     */
    protected function makeErrorResponse($code, $message)
    {
        $message = $message ?: 'Proxy error';
        return new Response($code, [
            'Content-Length' => strlen($message)
        ], $message);
    }

    /**
     * {@inheritdoc}
     */
    protected function closeAllProxyConnections()
    {
        foreach ($this->proxyConnections as $proxyConnection) {
            $proxyConnection->getConnection()->end($this->makeErrorResponse(500, 'The tunnel server has been closed'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleProxyConnectionTimeout()
    {
        foreach ($this->proxyConnections as $key=> $proxyConnection) {
            if ($proxyConnection->getWaitingTime() > 60) {
                $proxyConnection->getConnection()->end($this->makeErrorResponse(500, 'Waiting for more than 60 seconds without responding'));
                unset($this->proxyConnections[$key]);
            }
        }
    }
}