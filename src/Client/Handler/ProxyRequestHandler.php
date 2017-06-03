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

class ProxyRequestHandler extends Handler
{
    public function handle(MessageInterface $message)
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

        //Emit the event
        $this->client->getDispatcher()->dispatch(new Event(EventStore::RECEIVE_PROXY_REQUEST, $this, [
            'message' => $message,
            'proxyHost' => $proxyHost,
            'request' => $request
        ]));

        $response = $this->client->getHttpClient()->send($request);
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
}