<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use Slince\Event\Event;
use Spike\Client\EventStore;
use Spike\Exception\RuntimeException;
use Spike\Protocol\MessageInterface;
use Spike\Protocol\ProxyResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\Cookie\CookieJar;

class ProxyRequestHandler extends Handler
{
    protected function getProxyHost(RequestInterface $request)
    {
        $proxyHost = $request->getUri()->getHost();
        if ($request->getUri()->getPort()) {
            $proxyHost .= "{$request->getUri()->getPort()}";
        }
        return $proxyHost;
    }

    protected function applyForwardHost(RequestInterface $request, $forwardHost)
    {
        $parts = explode(':', $forwardHost);
        $uri = $request->getUri()->withHost($parts[0]);
        if (isset($parts[1])) {
            $uri = $uri->withPort($parts[1]);
        }
        return $request->withUri($uri);
    }

    protected function transfer(RequestInterface $request)
    {
        $jar = new CookieJar;
        $response = $this->client->getHttpClient()->send($request, [
            'cookies' => $jar
        ]);
        return $response;
    }

    public function handle(MessageInterface $message)
    {
        $forwardedConnectionId = $message->getHeader('Forwarded-Connection-Id');
        $request = $message->getRequest();

        $proxyHost = $this->getProxyHost($request);
        $forwardHost = $this->client->getForwardHost($proxyHost);
        if (!$forwardHost) {
            throw new RuntimeException(sprintf('The host "%s" is not supported by the client', $proxyHost));
        }
        $request = $this->applyForwardHost($request, $forwardHost);
        //Emit the event
        $this->client->getDispatcher()->dispatch(new Event(EventStore::RECEIVE_PROXY_REQUEST, $this, [
            'message' => $message,
            'proxyHost' => $proxyHost,
            'request' => $request
        ]));

        $response = $this->transfer($request);
        
        $proxyResponse = new ProxyResponse(0, $response, [
            'Forwarded-Connection-Id' => $forwardedConnectionId
        ]);
        $this->connection->write($proxyResponse);

        //Emit the event
        $this->client->getDispatcher()->dispatch(new Event(EventStore::SEND_PROXY_RESPONSE, $this, [
            'message' => $message,
            'proxyHost' => $proxyHost,
            'request' => $request,
            'proxyResponse'  => $proxyResponse
        ]));
    }

    protected function fixResponse(ResponseInterface $response, $proxyHost, $forwardHost)
    {
        $response = $response->withHeader('Content-Length', strlen((string)$response->getBody()));
        if ($response->hasHeader('Transfer-Encoding')) {
            $response = $response->withoutHeader('Transfer-Encoding');
        }
        return str_replace([$forwardHost, strstr($forwardHost)], [$proxyHost], Psr7\str($response));
    }
}