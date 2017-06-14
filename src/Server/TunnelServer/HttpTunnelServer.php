<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use React\Socket\ConnectionInterface;
use Spike\Buffer\HttpHeaderBuffer;
use Spike\Exception\UnsupportedProtocolException;

class HttpTunnelServer extends TunnelServer
{
    public function handleProxyConnection(ConnectionInterface $connection)
    {
        try {
            $buffer = new HttpHeaderBuffer($connection);
            $buffer->gather(function ($buffer) use ($connection) {
                $psrRequest = Psr7\parse_request($buffer);
                $host = $psrRequest->getUri()->getHost();
                if ($psrRequest->getUri()->getPort()) {
                    $host .= "{$psrRequest->getUri()->getPort()}";
                }
                if ($this->tunnel->supportProxyHost($host)) {
                    $this->tunnel->pipe($connection);
                    $this->pause();
                } else {
                    $body = sprintf('The host "%s" was not binding.', $host);
                    $response = $this->makeErrorResponse(404, $body);
                    $connection->end(Psr7\str($response));
                }
            });
        } catch (UnsupportedProtocolException $exception) {
            $response = $this->makeErrorResponse(404, $exception->getMessage());
            $connection->end(Psr7\str($response));
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