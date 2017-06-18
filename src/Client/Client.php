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
use Spike\Client\Tunnel\HttpTunnel;
use Spike\Client\Tunnel\TcpTunnel;
use Spike\Client\Tunnel\TunnelFactory;
use Spike\Client\Tunnel\TunnelInterface;
use Spike\Client\TunnelClient\TcpTunnelClient;
use Spike\Exception\InvalidArgumentException;
use Spike\Parser\SpikeParser;
use Spike\Protocol\Spike;
use Spike\Protocol\SpikeInterface;

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
     * Array of tunnel connections
     * @var ConnectionInterface[]
     */
    protected $tunnelConnections;
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
            $tunnel = TunnelFactory::fromArray($info);
            $tunnels[] = $tunnel;
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
            $this->connection = $connection;
            $this->setControlConnectionForTunnels($connection);
            $this->requestAuth($connection);
            $this->handleConnection($connection);
        });
        $this->dispatcher->dispatch(EventStore::CLIENT_RUN);
        $this->loop->run();
    }

    public function handleConnection(ConnectionInterface $connection)
    {
        $parser = new SpikeParser();
        $connection->on('data', function($data) use($parser, $connection){
            $parser->pushIncoming($data);
            $messages = $parser->parse();
            foreach ($messages as $message) {
                $message = Spike::fromString($message);
                $this->dispatcher->dispatch(new Event(EventStore::RECEIVE_MESSAGE, $this, [
                    'message' => $message,
                    'connection' => $connection
                ]));
                $this->createMessageHandler($message, $connection)->handle($message);
            }
        });
    }

    protected function requestAuth(ConnectionInterface $connection)
    {
        $authInfo = [
            'os' => PHP_OS,
            'username' => '',
            'password' => '',
            'version' => '',
        ];
        $connection->write(new Spike('auth', $authInfo));
    }

    protected function setControlConnectionForTunnels(ConnectionInterface $connection)
    {
        foreach ($this->tunnels as $tunnel) {
            $tunnel->setControlConnection($connection);
        }
    }

    public function createTunnelConnection(TunnelInterface $tunnel)
    {
        $connector = new Connector($this->loop);
        $connector->connect($this->serverAddress)->then(function(ConnectionInterface $connection) use ($tunnel){
            $this->tunnelConnections[] = $connection;
            $tunnel->setConnection($connection); //sets tunnel connection for the tunnel
            $connection->write(new Spike('register_proxy', $tunnel->toArray()));
            $this->handleTunnelConnection($connection);
        });
    }

    public function handleTunnelConnection(ConnectionInterface $connection)
    {
        $parser = new SpikeParser();
        $connection->on('data', function($data) use($parser, $connection){
            $parser->pushIncoming($data);
            $protocol = $parser->parseFirst();
            if ($protocol) {
                echo $protocol;
                $connection->removeAllListeners('data');
                $message = Spike::fromString($protocol);
                if ($message->getAction() == 'start_proxy') {
                    $tunnelInfo = $message->getBody();
                    $tunnel = $this->findTunnel($tunnelInfo);
                    if ($tunnel ===  false) {
                        throw new InvalidArgumentException("Can not find the matching tunnel");
                    }
                    $tunnel->pushBuffer($parser->getRestData());
                    if ($tunnel instanceof HttpTunnel) {
                        $localAddress = $tunnel->getLocalHost($tunnelInfo['proxyHost']);
                    }  else {
                        $tunnel->getHost();
                    }
                    $this->createTunnelClient($tunnel, $localAddress);
                }
                $this->dispatcher->dispatch(new Event(EventStore::RECEIVE_MESSAGE, $this, [
                    'message' => $message,
                    'connection' => $connection
                ]));
            }
        });
    }

    /**
     * @return ConnectionInterface[]
     */
    public function getTunnelConnections()
    {
        return $this->tunnelConnections;
    }

    public function createTunnelClient(TunnelInterface $tunnel, $localAddress)
    {
        $tunnelClient = new TcpTunnelClient($tunnel, $localAddress, $this->loop);
        $tunnelClient->run();
    }

    /**
     * @param string $id
     */
    public function setClientId($id)
    {
        $this->id = $id;
        Spike::setGlobalHeader('Client-ID', $id);
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
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Finds the matching tunnel
     * @param array $tunnelInfo
     * @return bool|TunnelInterface
     */
    public function findTunnel($tunnelInfo)
    {
        foreach ($this->getTunnels() as $tunnel) {
            $matching = $tunnel->getRemotePort() == $tunnelInfo['port']
                && (
                    $tunnel instanceof TcpTunnel
                    || (
                        !isset($tunnelInfo['proxyHost'])
                        || $tunnel->supportProxyHost($tunnelInfo['proxyHost'])
                    )
                );
            if ($matching) {
                return $tunnel;
            }
        }
        return false;
    }

    /**
     * Creates the handler for the received message
     * @param SpikeInterface $message
     * @param ConnectionInterface $connection
     * @return Handler\HandlerInterface
     */
    protected function createMessageHandler(SpikeInterface $message, ConnectionInterface $connection)
    {
        switch ($message->getAction()) {
            case 'auth_response':
                $handler = new Handler\AuthResponseHandler($this, $connection);
                break;
            case 'register_tunnel_response':
                $handler = new Handler\RegisterTunnelResponseHandler($this, $connection);
                break;
            case 'request_proxy':
                $handler = new Handler\RequestProxyHandler($this, $connection);
                break;
            case 'start_proxy':
                $handler = new Handler\StartProxyHandler($this, $connection);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Cannot find handler for message type: "%s"',
                    get_class($message)
                ));
        }
        return $handler;
    }
}