<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Psr\Http\Message\RequestInterface;
use Spike\Exception\RuntimeException;
use Spike\Protocol\ProxyRequest;
use Spike\Server\ProxyConnection;

class ProxyRequestHandler extends Handler
{
    public function handle($request)
    {
        $proxyConnection = new ProxyConnection($this->connection);
        $this->server->addProxyConnection($proxyConnection);

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