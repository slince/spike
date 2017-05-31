<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use Spike\Exception\RuntimeException;
use Spike\Protocol\ProxyResponse;

class ProxyRequestHandler extends Handler
{
    public function handle($message)
    {
        $forwardedConnectionId = $message->getHeader('Forwarded-Connection-Id');
        $request = $message->getRequest();

        $proxyHost = $request->getUri()->getHost();
        if ($request->getUri()->getPort()) {
            $proxyHost .= "{$request->getUri()->getPort()}";
        }
        $forwardHost = $this->client->getForwardHost($proxyHost);
        if (!$forwardHost) {
            throw new RuntimeException(sprintf('The host "%s" is not supported by the client', $proxyHost));
        }

        list($host, $port) = explode(':', $forwardHost);
        $uri = $request->getUri()->withHost($host)->withPort($port);
        $request = $request->withUri($uri);
        $response = $this->client->getHttpClient()->send($request);

        $this->connection->write(new ProxyResponse(0, $response, [
            'Forwarded-Connection-Id' => $forwardedConnectionId
        ]));
    }
}