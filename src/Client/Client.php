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
use Spike\Buffer\BufferInterface;
use Spike\Client\Handler\HandlerInterface;
use Spike\Client\Handler\ProxyRequestHandler;
use Spike\Client\Tunnel\HttpTunnel;
use Spike\Client\Tunnel\TunnelFactory;
use Spike\Client\Tunnel\TunnelInterface;
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\MessageInterface;
use Spike\Buffer\SpikeBuffer;
use Spike\Protocol\Spike;

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
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var string
     */
    protected $serverAddress;

    /**
     * @var array
     */
    protected $credential;

    /**
     * @var string
     */
    protected $id;

    /**
     * Tunnels
     * @var TunnelInterface
     */
    protected $tunnels = [];

    /**
     * @var ProxyContext
     */
    protected $proxyContext;

    public function __construct($serverAddress, $tunnels, LoopInterface $loop = null, Dispatcher $dispatcher = null)
    {
        $this->serverAddress = $serverAddress;
        $this->dispatcher = $dispatcher ?: new Dispatcher();
        $this->loop = $loop ?: LoopFactory::create();
        $this->connector = new Connector($this->loop);
        $this->tunnels = $this->createTunnels($tunnels);
    }

    /**
     * Checks whether the client is authorized
     * @return bool
     */
    public function isAuthorized()
    {
        return !empty($this->id);
    }

    /**
     * @param array $data
     * @return TunnelInterface[]
     */
    protected function createTunnels($data)
    {
        $tunnels = [];
        foreach ($data as $info) {
            $tunnels[] = TunnelFactory::fromArray($info);
        }
        return $tunnels;
    }

    public function run()
    {
        $this->connector->connect($this->serverAddress)->then(function(ConnectionInterface $connection){
            //Emit the event
            $this->dispatcher->dispatch(new Event(EventStore::CONNECT_TO_SERVER, $this, [
                'connection' => $connection
            ]));
            $this->handleConnection();
        });
        $this->dispatcher->dispatch(EventStore::CLIENT_RUN);
        $this->loop->run();
    }

    protected function handleConnection()
    {
        $this->requestAuthorization();
        try {
            $buffer = new SpikeBuffer($this->connection);
            $buffer->gather(function(BufferInterface $buffer){
                $message = Spike::fromString($buffer);
                $this->dispatcher->dispatch(new Event(EventStore::RECEIVE_MESSAGE, $this, [
                    'message' => $message,
                    'connection' => $this->connection
                ]));
                $this->createMessageHandler($message)->handle($message);
                $buffer->flush(); //Flush the buffer and continue gather message
            });
        } catch (InvalidArgumentException $exception) {
            $this->dispatcher->dispatch(new Event(EventStore::CONNECTION_ERROR, $this, [
                'connection' => $this->connection,
                'exception' => $exception,
            ]));
        }
    }

    public function requestAuthorization()
    {
        $authInfo = [
            'os' => PHP_OS,
            'username' => '',
            'password' => '',
            'version' => '',
        ];
        $this->connection->write(new Spike('auth', $authInfo));
    }

    /**
     * @param string $id
     */
    public function setClientId($id)
    {
        $this->id = $id;
    }

    /**
     * @return TunnelInterface[]
     */
    public function getTunnels()
    {
        return $this->tunnels;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param ProxyContext $proxyContext
     */
    public function setProxyContext(ProxyContext $proxyContext)
    {
        $this->proxyContext = $proxyContext;
    }

    public function createTunnelClient(ConnectionInterface $connection)
    {
        $tunnel = $this->proxyContext->getTunnel();
        if ($tunnel instanceof HttpTunnel) {
            $address = $this->proxyContext->getArgument('forwardHost');
        } else {
            $address = $tunnel->getHost();
        }
        $this->tunnelClient = new TunnelClient($address, $connection, $this->loop);
    }

    /**
     * Creates the handler for the received message
     * @param MessageInterface $message
     * @return HandlerInterface
     */
    protected function createMessageHandler($message)
    {
        if ($message instanceof StartProxy) {
            $handler = new ProxyRequestHandler($this, $this->connection);
        } else {
            throw new InvalidArgumentException(sprintf('Cannot find handler for message type: "%s"',
                get_class($message)
            ));
        }
        return $handler;
    }
}