<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Slince\Event\Event;
use GuzzleHttp\Psr7;
use Spike\Exception\RuntimeException;
use Spike\Protocol\MessageInterface;
use Spike\Server;
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
        $response = $message->getResponse();
        //Use content-length mode
        if ($response->hasHeader('Transfer-Encoding')) {
            $response = $response->withoutHeader('Transfer-Encoding');
        }
        $response = $response->withHeader('Content-Length', strlen($response->getBody()));
        $response = $response->withHeader('X-Proxy-Agent', Server::NAME . ';version:'  .  Server::VERSION);
        $this->server->getDispatcher()->dispatch(new Event(EventStore::RECEIVE_PROXY_RESPONSE, $this, [
            'proxyConnection' => $proxyConnection,
            'proxyResponse' => $message,
            'response' => $response
        ]));
        $proxyConnection->getConnection()->write(Psr7\str($response));
    }
}