<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use Slince\Event\Event;
use Spike\Client\EventStore;
use Spike\Client\Exception\UnsupportedHostException;
use Spike\Exception\RuntimeException;
use Spike\Protocol\MessageInterface;
use Spike\Protocol\ProxyResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Cookie\CookieJar;

class ProxyRequestHandler extends MessageHandler
{
    public function handle(MessageInterface $message)
    {
        $forwardedConnectionId = $message->getHeader('Forwarded-Connection-Id');
        $originRequest = $message->getRequest();

        $proxyHost = $this->getProxyHost($originRequest);
        $forwardHost = $this->client->getForwardHost($proxyHost);
        if (!$forwardHost) {
            throw new UnsupportedHostException($forwardedConnectionId, sprintf('The host "%s" is not supported by the client', $proxyHost));
        }
        $request = $this->applyForwardHost($originRequest, $forwardHost);
        //Emit the event
        $this->client->getDispatcher()->dispatch(new Event(EventStore::RECEIVE_PROXY_REQUEST, $this, [
            'message' => $message,
            'originRequest' => $originRequest,
            'request' => $request
        ]));

        $response = $this->transfer($request, $originRequest);
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

    protected function transfer(RequestInterface $request, RequestInterface $originRequest)
    {
        $jar = new CookieJar;
        $response = $this->client->getHttpClient()->send($request, [
            'cookies' => $jar
        ]);
        $cookies = $this->handleCookies($jar, $request, $originRequest);
        $response = $response->withHeader('Set-Cookie', $cookies);
        return $response;
    }

    protected function handleCookies(CookieJar $cookies, RequestInterface $request, RequestInterface $originRequest)
    {
        $handledCookies = [];
        $forwardHost = $request->getUri()->getHost();
        $proxyHost = $originRequest->getUri()->getHost();
        foreach ($cookies as $cookie) {
            if ($cookie->matchesDomain($forwardHost)) {
                $cookie->setDomain($proxyHost);
            }
            $handledCookies[] = $cookie;
        }
        return $handledCookies;
    }
}