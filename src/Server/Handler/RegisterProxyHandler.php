<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Exception\BadRequestException;
use Spike\Protocol\SpikeInterface;
use Slince\Event\Event;
use Spike\Server\EventStore;

class RegisterProxyHandler extends RequireAuthHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        parent::handle($message);
        //Fires 'register_proxy' event
        $this->getDispatcher()->dispatch(new Event(EventStore::REGISTER_PROXY, $this, [
            'message' => $message
        ]));
        $tunnelServer = $this->server->getTunnelServers()->findByTunnelInfo($message->getBody());
        if (!$tunnelServer) {
            throw new BadRequestException("Can not find the tunnel server");
        }
        $this->connection->removeAllListeners();
        $tunnelServer->registerProxyConnection($this->connection, $message);
    }
}