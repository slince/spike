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
        $request = $message->getRequest();
        $host = $request->getUri()->getHost();
        if ($request->getUri()->getPort()) {
            $host .= "{$request->getUri()->getPort()}";
        }
        $proxyHost = $this->server->findProxyHost($host);
        if (is_null($proxyHost)) {
            throw new RuntimeException(sprintf('Cannot find the proxy client for the host "%s"', $host));
        }
        //Stores the proxy connection and proxy request
        $proxyConnection = new ProxyConnection($this->connection);
        $proxyRequest = new ProxyRequest($request, [
            'Forwarded-Connection-Id' => $proxyConnection->getId()
        ]);
        $proxyConnection->setProxyRequest($proxyRequest);
        $this->server->addProxyConnection($proxyConnection);

        $proxyHost->getConnection()->write($proxyRequest);
        $this->server->getDispatcher()->dispatch(new Event(EventStore::SEND_PROXY_REQUEST, $this, [
            'message' => $message,
            'proxyHost' => $proxyHost,
            'proxyRequest' => $proxyRequest
        ]));
    }
}