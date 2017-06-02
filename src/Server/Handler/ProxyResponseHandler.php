<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Exception\RuntimeException;
use GuzzleHttp\Psr7;
use Spike\Protocol\MessageInterface;

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
    }
}