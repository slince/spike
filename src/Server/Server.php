<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

use Cake\Collection\Collection;
use GuzzleHttp\Psr7;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\RegisterHostRequest;
use Spike\Protocol\MessageInterface;
use Spike\Protocol\ProxyRequest;
use Spike\Protocol\ProxyResponse;
use Spike\ProtocolFactory;
use Spike\Exception\RuntimeException;
use Spike\Server\Handler\HandlerInterface;
use Spike\Server\Handler\ProxyRequestHandler;
use Spike\Server\Handler\ProxyResponseHandler;
use Spike\Server\Handler\RegisterHostHandler;

class Server
{
    /**
     * @var Socket
     */
    protected $socket;

    /**
     * @var ProxyConnection[]
     */
    protected $proxyConnections;

    /**
     * @var ProxyHost[]
     */
    protected $proxyHosts;

    public function __construct($address, LoopInterface $loop = null)
    {
        if (is_null($loop)) {
            $loop = LoopFactory::create();
        }
        $this->loop = $loop;
        $this->socket = new Socket($address, $loop);
        $this->proxyHosts = new Collection([]);
    }

    /**
     * Run the server
     */
    public function run()
    {
        $this->socket->on('connection', function(ConnectionInterface $connection){
            $connection->on('data', function($data) use ($connection){
                $protocol = ProtocolFactory::create($data);
                $this->createHandler($protocol, $connection)->handle($protocol);
            });
            $connection->on('error', function($message){});
        });
        $this->loop->run();
    }

    /**
     * Gets all proxy connections
     * @return ProxyConnection[]
     */
    public function getProxyConnections()
    {
        return $this->proxyConnections;
    }

    /**
     * Gets the proxy hosts
     * @return Collection
     */
    public function getProxyHosts()
    {
        return $this->proxyHosts;
    }

    /**
     * Sets the proxy hosts of the server
     * @param Collection $proxyHosts
     */
    public function setProxyHosts($proxyHosts)
    {
        $this->proxyHosts = $proxyHosts;
    }

    /**
     * Adds a proxy host record
     * @param ProxyHost $proxyHost
     */
    public function addProxyHost(ProxyHost $proxyHost)
    {
        $this->proxyHosts[] = $proxyHost;
    }

    /**
     * Adds some proxy hosts
     * @param ProxyHost[] $proxyHosts
     */
    public function addProxyHosts($proxyHosts)
    {
        $this->proxyHosts += $proxyHosts;
    }

    /**
     * Finds the proxy host for the given host
     * @param string $host
     * @return null|ProxyHost
     */
    public function findProxyHost($host)
    {
        foreach ($this->proxyHosts as $proxyHost) {
            if ($proxyHost->getHost() == $host) {
                return $proxyHost;
            }
        }
        return null;
    }

    public function addProxyConnection(ProxyConnection $proxyConnection)
    {
        $this->proxyConnections[] = $proxyConnection;
    }

    /**
     * Finds the proxy connection by given id
     * @param string $connectionId
     * @return null|ProxyConnection
     */
    public function findProxyConnection($connectionId)
    {
        foreach ($this->proxyConnections as $proxyConnection) {
            if ($proxyConnection->getId() == $connectionId) {
                return $proxyConnection;
            }
        }
        return null;
    }

    /**
     * Creates the handler for the received message
     * @param $protocol
     * @param $connection
     * @return HandlerInterface
     */
    protected function createHandler($protocol, $connection)
    {
        if ($protocol instanceof RegisterHostRequest) {
            $handler = new RegisterHostHandler($this, $connection);
        } elseif ($protocol instanceof RequestInterface) {
            $handler = new ProxyRequestHandler($this, $connection);
        } elseif ($protocol instanceof ProxyResponse) {
            $handler = new ProxyResponseHandler($this, $connection);
        } else {
            throw new InvalidArgumentException(sprintf('Cannot find handler for message type: "%s"',
                gettype($protocol)
            ));
        }
        return $handler;
    }
}