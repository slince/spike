<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use React\Socket\ConnectionInterface;
use Spike\Buffer\HeaderBuffer;
use Spike\Protocol\HttpRequest;

class HttpTunnelServer extends TunnelServer
{
    public function handleConnection(ConnectionInterface $connection)
    {
        $buffer = new HeaderBuffer($connection);
        $buffer->gather(function($buffer) use($connection){
            $psrRequest = HttpRequest::fromString($buffer)->getRequest();
            $host = $psrRequest->getUri()->getHost();
            if ($psrRequest->getUri()->getPort()) {
                $host .= "{$psrRequest->getUri()->getPort()}";
            }
            if ($this->tunnel->supportHost($host)) {
                $this->tunnel->getConnection()->write($buffer);
                $this->tunnel->pipe($connection);
            }
        });
    }
}