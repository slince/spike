<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\TunnelClient;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use Spike\Buffer\SpikeBuffer;
use Spike\Client\Tunnel\HttpTunnel;
use Spike\Client\Tunnel\TunnelInterface;
use Spike\Parser\SpikeParser;
use Spike\Protocol\Spike;

abstract class TunnelClient implements TunnelClientInterface
{
    protected $tunnel;

    protected $loop;

    protected $serverAddress;

    protected $localConnector;

    /**
     * @var Connector
     */
    protected $serverConnector;

    public function __construct(TunnelInterface $tunnel, $serverAddress, LoopInterface $loop)
    {
        $this->tunnel = $tunnel;
        $this->serverAddress = $serverAddress;
        $this->loop = $loop;
    }

    public function run()
    {
        $this->serverConnector = new Connector($this->loop);
        $this->serverConnector->connect($this->serverAddress)
            ->then([$this, 'handleServerConnection']);
    }

    public function handleServerConnection(ConnectionInterface $connection)
    {
        $this->tunnel->setConnection($connection); //sets tunnel connection for the tunnel
        $connection->write(new Spike('register_proxy', $this->tunnel->toArray()));

        $parser = new SpikeParser();
        $connection->on('data', function($data) use($parser, $connection){
            $parser->pushIncoming($data);
            $protocol = $parser->parseFirst();
            if ($protocol) {
                echo $protocol;
                $connection->removeAllListeners('data');
                $message = Spike::fromString($protocol);
                if ($message->getAction() == 'start_proxy') {
//                    $tunnelInfo = $message->getBody();
//                    $tunnel = $this->findTunnel($tunnelInfo);
//                    if ($tunnel ===  false) {
//                        throw new InvalidArgumentException("Can not find the matching tunnel");
//                    }
                    $this->tunnel->pushBuffer($parser->getRestData());
                    if ($this->tunnel instanceof HttpTunnel) {
                        $localAddress = $this->tunnel->getLocalHost($this->tunnel->getProxyHost());
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
        $this->localConnector = new Connector($this->loop);
        $this->localConnector->connect($address)->then([$this, 'handleLocalConnection']);
    }

    /**
     * {@inheritdoc}
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }
}