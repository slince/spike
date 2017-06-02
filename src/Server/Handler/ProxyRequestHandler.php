<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Slince\Event\Event;
use Spike\Protocol\MessageInterface;
use Spike\Exception\RuntimeException;
use Spike\Protocol\ProxyRequest;
use Spike\Server\EventStore;
use Spike\Server\ProxyConnection;

class ProxyRequestHandler extends Handler
{
    public function handle(MessageInterface $message)
    {
        $proxyConnection = new ProxyConnection($this->connection);
        $this->server->addProxyConnection($proxyConnection);
        $request = $message->getRequest();
        $host = $request->getUri()->getHost();
        if ($request->getUri()->getPort()) {
            $host .= "{$request->getUri()->getPort()}";
        }
        $proxyHost = $this->server->findProxyHost($host);
        if (is_null($proxyHost)) {
            throw new RuntimeException(sprintf('Cannot find proxy client for the host "%s"', $host));
        }
        $proxyRequest = new ProxyRequest($request, [
            'Forwarded-Connection-Id' => $proxyConnection->getId()
        ]);
        $proxyHost->getConnection()->write($proxyRequest);
        $this->server->getDispatcher()->dispatch(new Event(EventStore::SEND_PROXY_REQUEST, $this, [
            'message' => $message,
            'proxyHost' => $proxyHost,
            'proxyRequest' => $proxyRequest
        ]));
    }
}