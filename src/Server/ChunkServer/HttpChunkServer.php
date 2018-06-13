<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Server\ChunkServer;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use function Slince\Common\httpHeaderBuffer;
use Spike\Common\Protocol\HttpHeaderParser;

/**
 * @codeCoverageIgnore
 */
class HttpChunkServer extends TcpChunkServer
{
    /**
     * {@inheritdoc}
     */
    public function handlePublicConnection(PublicConnection $publicConnection)
    {
        $parser = new HttpHeaderParser();
        httpHeaderBuffer($publicConnection->getConnection(), $parser)->then(function($message) use ($parser, $publicConnection){
            $psrRequest = Psr7\parse_request($message);
            $host = $psrRequest->getUri()->getHost();
            if ($this->tunnel->supportProxyHost($host)) {
                $this->tunnel->setProxyHost($host);
                $httpMessage = $message . $parser->getRemainingChunk();
                $publicConnection->setInitBuffer($httpMessage);
                parent::handlePublicConnection($publicConnection);
            } else {
                $body = sprintf('The host "%s" was not bound.', $host);
                $response = $this->makeErrorResponse(404, $body);
                $publicConnection->end(Psr7\str($response));
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
    protected function closePublicConnection(PublicConnection $publicConnection, $message = null)
    {
        $publicConnection->end(Psr7\str($this->makeErrorResponse(500, $message ?: 'Timeout')));
    }
}