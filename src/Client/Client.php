<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client;

use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use Slince\Event\Dispatcher;
use Slince\Event\Event;
use Spike\Client\Handler\HandlerInterface;
use Spike\Client\Handler\ProxyRequestHandler;
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\RegisterHostRequest;
use Spike\ProtocolFactory;
use Spike\Protocol\MessageInterface;
use Spike\Protocol\ProxyRequest;
use GuzzleHttp\Client as HttpClient;
use Spike\Protocol\ProxyResponse;
use Spike\Server\ProxyHost;

class Client
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var Connector
     */
    protected $connector;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $serverAddress;

    /**
     * Proxy host to forward host map
     * @var array
     */
    protected $forwardHosts = [];

    public function __construct($serverAddress, LoopInterface $loop = null, HttpClient $client = null, Dispatcher $dispatcher = null)
    {
        $this->serverAddress = $serverAddress;
        $this->httpClient = $client ?: new HttpClient();
        $this->loop = $loop ?: LoopFactory::create();
        $this->connector = new Connector($loop);
        $this->dispatcher = $dispatcher ?: new Dispatcher();
    }

    public function run()
    {
        $this->connector->connect($this->serverAddress)->then(function(ConnectionInterface $connection){
            //Emit the event
            $this->dispatcher->dispatch(new Event(EventStore::CONNECT_TO_SERVER, $this, [
                'connection' => $connection
            ]));
            //Reports the proxy hosts
            $this->transferProxyHosts($connection);
            $connection->on('data', function($data) use ($connection){
                $message = ProtocolFactory::create($data);
                $this->dispatcher->dispatch(new Event(EventStore::RECEIVE_MESSAGE, $this, [
                    'message' => $message,
                    'connection' => $connection
                ]));
                $this->createHandler($message, $connection)->handle($message);
            });
        });
        $this->loop->run();
    }

    /**
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Gets all forward hosts
     * @return array
     */
    public function getForwardHosts()
    {
        return $this->forwardHosts;
    }

    /**
     * Adds a forward host
     * @param string $proxyHost
     * @param string $forwardHost
     */
    public function addForwardHost($proxyHost, $forwardHost)
    {
        $this->forwardHosts[$proxyHost] = $forwardHost;
    }

    /**
     * Gets the forward host for the host
     * @param string $proxyHost
     * @return string|null
     */
    public function getForwardHost($proxyHost)
    {
        return isset($this->forwardHosts[$proxyHost]) ? $this->forwardHosts[$proxyHost] : null;
    }

    /**
     * Reports the proxy hosts to the server
     * @param ConnectionInterface $connection
     */
    protected function transferProxyHosts(ConnectionInterface $connection)
    {
        $proxyHosts = array_keys($this->forwardHosts);
        $this->dispatcher->dispatch(new Event(EventStore::TRANSFER_PROXY_HOSTS, $this, [
            'proxyHosts' => $proxyHosts
        ]));
        $connection->write(new RegisterHostRequest($proxyHosts));
    }

    /**
     * Creates the handler for the received message
     * @param MessageInterface $message
     * @param ConnectionInterface $connection
     * @return HandlerInterface
     */
    protected function createHandler($message, $connection)
    {
        if ($message instanceof ProxyHost) {
            $handler = new ProxyRequestHandler($this, $connection);
        } else {
            throw new InvalidArgumentException(sprintf('Cannot find handler for message type: "%s"',
                gettype($message)
            ));
        }
        return $handler;
    }
}