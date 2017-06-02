<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\MessageInterface;
use Spike\Protocol\RegisterHostResponse;
use Spike\Server\ProxyHost;

class RegisterHostHandler extends Handler
{
    public function handle(MessageInterface $message)
    {
        $proxyHosts = array_map(function($host){
            return new ProxyHost($host, $this->connection);
        }, $message->getAddingDomains());
        $this->server->addProxyHosts($proxyHosts);
        //Tell client ok
        $this->connection->write(new RegisterHostResponse(0));
    }
}