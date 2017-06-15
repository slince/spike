<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use React\Socket\ConnectionInterface;
use Spike\Buffer\BufferInterface;
use Spike\Buffer\HttpHeaderBuffer;
use Spike\Exception\UnsupportedProtocolException;
use Spike\Protocol\Spike;

class HttpTunnelServer extends TunnelServer
{
    public function handleProxyConnection(ConnectionInterface $proxyConnection)
    {
        try {
            $buffer = new HttpHeaderBuffer($proxyConnection);
            $buffer->gather(function (BufferInterface $buffer) use ($proxyConnection) {
                $psrRequest = Psr7\parse_request($buffer->getMessage());
                $host = $psrRequest->getUri()->getHost();
                if ($this->tunnel->supportProxyHost($host)) {
                    $this->tunnel->setProxyHost($host);
                    $this->tunnel->getControlConnection()->write(new Spike('request_proxy', $this->tunnel->toArray()));
                    $this->tunnel->pipe($proxyConnection);
                    $httpMessage = $buffer->getMessage() . $buffer->getContent();
                    $buffer->destroy();
                    $proxyConnection->emit('data', [$httpMessage]);
                } else {
                    $body = sprintf('The host "%s" was not bound.', $host);
                    $response = $this->makeErrorResponse(404, $body);
                    $proxyConnection->end(Psr7\str($response));
                }
            });
        } catch (UnsupportedProtocolException $exception) {
            $response = $this->makeErrorResponse(404, $exception->getMessage());
            $proxyConnection->end(Psr7\str($response));
        }
    }

    protected function makeErrorResponse($code, $message)
    {
        $message = $message ?: 'Proxy error';
        return new Response($code, [
            'Content-Length' => strlen($message)
        ], $message);
    }
}