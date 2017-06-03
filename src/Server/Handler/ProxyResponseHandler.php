<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slince\Event\Event;
use GuzzleHttp\Psr7;
use Spike\Exception\RuntimeException;
use Spike\Protocol\MessageInterface;
use Spike\Server\EventStore;

class ProxyResponseHandler extends Handler
{
    /**
     * {@inheritdoc}
     */
    public function handle(MessageInterface $message)
    {
        $forwardedConnectionId = $message->getHeader('Forwarded-Connection-Id');
        if (!$forwardedConnectionId || !($proxyConnection = $this->server->findProxyConnection($forwardedConnectionId))) {
            throw new RuntimeException('Lose Connection or the connection has been close');
        }
        $proxyConnection->getConnection()->write(Psr7\str($message->getResponse()));
        $this->server->getDispatcher()->dispatch(new Event(EventStore::RECEIVE_PROXY_RESPONSE, $this, [
            'proxyConnection' => $proxyConnection,
            'proxyResponse' => $message
        ]));
    }

    protected function fixResponse(ResponseInterface $response, RequestInterface $request)
    {
        $response = $response->withHeader('Content-Length', strlen((string)$response->getBody()));
        if ($response->hasHeader('Transfer-Encoding')) {
            $response = $response->withoutHeader('Transfer-Encoding');
        }
    }
}