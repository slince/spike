<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use GuzzleHttp\Psr7;
use Spike\Client\Exception\UnsupportedHostException;
use Spike\Exception\RuntimeException;
use Spike\Protocol\MessageInterface;
use Spike\Server\EventStore;
use Slince\Event\Event;
use Spike\Server;

class ClientExceptionHandler extends Handler
{
    /**
     * {@inheritdoc}
     */
    public function handle(MessageInterface $message)
    {
        $clientException  = $message->getException();
        if ($clientException instanceof UnsupportedHostException) {
            $forwardedConnectionId = $clientException->getConnectionId();
            if (!$forwardedConnectionId || !($proxyConnection = $this->server->findProxyConnection($forwardedConnectionId))) {
                throw new RuntimeException('Lose Connection or the connection has been close');
            }
            $response = $this->createResponse();
            $response = $response->withHeader('X-Proxy-Agent', Server::NAME . ';version:'  .  Server::VERSION);
            $this->server->getDispatcher()->dispatch(new Event(EventStore::RECEIVE_CLIENT_EXCEPTION, $this, [
                'proxyConnection' => $proxyConnection,
            ]));
            $proxyConnection->getConnection()->write(Psr7\str($response));
        }
    }

    /**
     * @return Psr7\Response
     */
    protected function createResponse()
    {
        $body = 'The proxy client sends an exception';
        return new Psr7\Response(500, [
            'Content-Length' => strlen($body)
        ], $body);
    }
}