<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\MessageInterface;
use Spike\Exception\RuntimeException;
use Spike\Protocol\ProxyRequest;
use Spike\Server\ProxyConnection;

class ProxyRequestHandler extends Handler
{
    public function handle(MessageInterface $message)
    {
        $proxyConnection = new ProxyConnection($this->connection);
        $this->server->addProxyConnection($proxyConnection);
        $request = $message->getRequest();
        $forwardHost = $request->getUri()->getHost();
        if ($request->getUri()->getPort()) {
            $forwardHost .= "{$request->getUri()->getPort()}";
        }
        $proxyHost = $this->server->findProxyHost($forwardHost);
        if (is_null($proxyHost)) {
            throw new RuntimeException(sprintf('Cannot find proxy client for the host "%s"', $forwardHost));
        }
        $proxyRequest = new ProxyRequest($request, [
            'Forwarded-Connection-Id' => $proxyConnection->getId()
        ]);
        $proxyHost->getConnection()->write($proxyRequest);
    }
}