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
use Spike\Protocol\DomainRegisterRequest;
use Spike\Protocol\MessageInterface;
use Spike\Protocol\ProxyRequest;
use Spike\Protocol\ProxyResponse;
use Spike\ProtocolFactory;
use Spike\Exception\RuntimeException;

class Server
{
    /**
     * @var ConnectionInterface[]
     */
    protected $proxyClients;

    /**
     * @var ConnectionInterface[]
     */
    protected $clients;

    /**
     * @var Socket
     */
    protected $socket;

    /**
     * @var Collection
     */
    protected $domainMap;

    public function __construct($address, LoopInterface $loop = null)
    {
        if (is_null($loop)) {
            $loop = LoopFactory::create();
        }
        $this->loop = $loop;
        $this->socket = new Socket($address, $loop);
        $this->socket->on('connection', function(ConnectionInterface $connection){
            $connection->on('data', function($data) use ($connection){
                $protocol = ProtocolFactory::create($data);
                if ($protocol === false) {
                    $connection->close();
                }
                $this->acceptConnection($connection, $protocol);
            });
            $connection->on('error', function($message){
                var_dump($message);
            });
        });
        $this->domainMap = new Collection([]);
    }

    public function run()
    {
        echo 'server running', PHP_EOL;
        $this->loop->run();
    }

    protected function acceptConnection(ConnectionInterface $connection, $protocol)
    {
        if ($protocol instanceof DomainRegisterRequest) {
            $this->handleDomainRegister($protocol, $connection);
            $this->proxyClients[] = $connection;
        } elseif ($protocol instanceof RequestInterface) {
            $connectionId = spl_object_hash($connection);
            $this->handleProxyRequest($protocol, $connection, $connectionId);
            $this->clients[$connectionId] = $connection;
        } elseif ($protocol instanceof ProxyResponse) {
            $this->handleProxyResponse($protocol, $connection);
        }
    }

    protected function handleDomainRegister(DomainRegisterRequest $protocol, ConnectionInterface $connection)
    {
        $this->domainMap = $this->domainMap->append(array_map(function($domain) use ($connection){
            return new DomainMapRecord($domain, $connection);
        }, $protocol->getAddingDomains()));
        print_r(count($this->domainMap->toArray()));
    }

    protected function handleProxyRequest(RequestInterface $protocol, ConnectionInterface $connection, $connectionId)
    {
        $host = $protocol->getUri()->getHost() .
            ($protocol->getUri()->getPort() ? ":{$protocol->getUri()->getPort()}" : '');
        $client = $this->findProxyClient($host);
        $proxyRequest = new ProxyRequest($protocol, [
            'Forwarded-Connection-Id' => $connectionId
        ]);
        $client->write($proxyRequest);
    }

    protected function handleProxyResponse(ProxyResponse $protocol, ConnectionInterface $connection)
    {
        $forwardedConnectionId = $protocol->getHeader('Forwarded-Connection-Id');
        if (!$forwardedConnectionId || !isset($this->clients[$forwardedConnectionId])) {
            $connection->write('Lose connection');
            return false;
        }
        $client = $this->clients[$forwardedConnectionId];
        $client->write(Psr7\str($protocol->getResponse()));
    }

    /**
     * @param string $host
     * @return ConnectionInterface
     */
    protected function findProxyClient($host)
    {
        $record = $this->domainMap->filter(function(DomainMapRecord $record) use ($host){
            return $record->getDomain() == $host;
        })->first();
        if (is_null($record)) {
            throw new RuntimeException("Cannot find proxy client for the request");
        }
        return $record->getConnection();
    }
}