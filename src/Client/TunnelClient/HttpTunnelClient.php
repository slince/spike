<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\TunnelClient;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;

class HttpTunnelClient extends TunnelClient
{
    /**
     * {@inheritdoc}
     */
    public function handleConnectLocalError(\Exception $exception)
    {
        $response = $this->makeErrorResponse(500, $exception->getMessage());
        $this->proxyConnection->end(Psr7\str($response));
//        $this->close();
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
}