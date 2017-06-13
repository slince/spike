<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client;

use GuzzleHttp\Client as HttpClient;
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use Slince\Event\Dispatcher;
use Slince\Event\Event;
use Spike\Buffer\BufferInterface;
use Spike\ChunkBuffer;
use Spike\Client\Handler\HandlerInterface;
use Spike\Client\Handler\ProxyRequestHandler;
use Spike\Client\Handler\RegisterHostResponseHandler;
use Spike\Client\Tunnel\HttpTunnel;
use Spike\Client\Tunnel\TunnelFactory;
use Spike\Client\Tunnel\TunnelInterface;
use Spike\Exception\BadRequestException;
use Spike\Exception\InvalidArgumentException;
use Spike\Exception\RuntimeException;
use Spike\Protocol\AuthRequest;
use Spike\Protocol\RegisterHostRequest;
use Spike\Protocol\RegisterHostResponse;
use Spike\Protocol\ProtocolFactory;
use Spike\Protocol\MessageInterface;
use Spike\Protocol\ProxyRequest;
use Spike\Buffer\HttpBuffer;
use Spike\Buffer\SpikeBuffer;
use Spike\Protocol\RegisterTunnel;
use Spike\Protocol\StartProxy;

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
            //Auth
            $this->getAuth();
            //Reports the proxy hosts
            $this->transferTunnels();
            $this->handleConnection();
        });
        $this->dispatcher->dispatch(EventStore::CLIENT_RUN);
        $this->loop->run();
    }

    public function getAuth()
    {
        $authInfo = [
            'os' => PHP_OS,
            'username' => '',
            'password' => '',
            'version' => '',
        ];
        $this->connection->write(new AuthRequest($authInfo));
    }

    protected function handleConnection()
    {
        if (!$this->proxyContext) {
            try {
                $buffer = new SpikeBuffer($this->connection);
                $buffer->gather(function(BufferInterface $buffer){
                    $message = ProtocolFactory::create($buffer);
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
     * Reports the proxy hosts to the server
     */
    protected function transferTunnels()
    {
        $this->dispatcher->dispatch(new Event(EventStore::REGISTER_TUNNELS, $this, [
            'tunnels' => $this->tunnels
        ]));
        foreach ($this->tunnels as $tunnel) {
            $this->connection->write(new RegisterTunnel($tunnel->toArray()));
            break;
        }
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