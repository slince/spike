<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\TunnelClient;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use Spike\Tunnel\HttpTunnel;
use Spike\Tunnel\TunnelInterface;
use Spike\Parser\SpikeParser;
use Spike\Protocol\Spike;

abstract class TunnelClient implements TunnelClientInterface
{
    /**
     * @var TunnelInterface
     */
    protected $tunnel;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var string
     */
    protected $serverAddress;

    /**
     * @var Connector
     */
    protected $serverConnector;

    /**
     * The tunnel connection
     * @var ConnectionInterface
     */
    protected $tunnelConnection;

    /**
     * @var string
     */
    protected $proxyConnectionId;

    /**
     * @var string
     */
    protected $initBuffer;

    public function __construct(TunnelInterface $tunnel, $proxyConnectionId, $serverAddress, LoopInterface $loop)
    {
        $this->tunnel = $tunnel;
        $this->proxyConnectionId = $proxyConnectionId;
        $this->serverAddress = $serverAddress;
        $this->loop = $loop;
    }

    public function run()
    {
        $serverConnector = new Connector($this->loop);
        $serverConnector->connect($this->serverAddress)
            ->then([$this, 'handleServerConnection']);
    }

    public function handleServerConnection(ConnectionInterface $connection)
    {
        $this->tunnelConnection = $connection;
        $connection->write(new Spike('register_proxy', $this->tunnel->toArray(), [
            'Proxy-Connection-ID' => $this->proxyConnectionId
        ]));

        $parser = new SpikeParser();
        $connection->on('data', function($data) use($parser, $connection){
            $parser->pushIncoming($data);
            $protocol = $parser->parseFirst();
            if ($protocol) {
                echo $protocol;
                $connection->removeAllListeners('data');
                $message = Spike::fromString($protocol);
                if ($message->getAction() == 'start_proxy') {
                    $this->initBuffer = $parser->getRestData();
                    if ($this->tunnel instanceof HttpTunnel) {
                        $localAddress = $this->tunnel->getForwardHost($this->tunnel->getProxyHost());
                    }  else {
                        $localAddress = $this->tunnel->getHost();
                    }
                    $this->createLocalConnector($localAddress);
                }
            }
        });
    }

    protected function createLocalConnector($address)
    {
        $localConnector = new Connector($this->loop);
        $localConnector->connect($address)->then([$this, 'handleLocalConnection']);
    }

    /**
     * {@inheritdoc}
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }
}