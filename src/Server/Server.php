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
use React\Socket\Connection;
use React\Socket\Server as Socket;
use Spike\Protocol\DomainRegisterRequest;
use Spike\Protocol\Factory;
use Spike\Protocol\HttpRequest;
use Spike\Protocol\ProtocolInterface;
use Spike\Protocol\ProxyRequest;
use Spike\Protocol\ProxyResponse;

class Server
{
    /**
     * @var Connection[]
     */
    protected $proxyClients;

    /**
     * @var Connection[]
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

    public function __construct(Socket $socket)
    {
        $this->socket = $socket;
        $this->socket->on('connection', function(Connection $connection){
            $connection->on('data', function($data) use ($connection){
                $protocol = Factory::create($data);
                if ($protocol === false) {
                    $connection->close();
                }
                $this->acceptConnection($connection, $protocol);
            });
        });
    }

    protected function acceptConnection(Connection $connection, ProtocolInterface $protocol)
    {
        if ($protocol instanceof DomainRegisterRequest) {
            $this->handleDomainRegister($protocol, $connection);
            $this->proxyClients[] = $connection;
        } elseif ($protocol instanceof HttpRequest) {
            $uid = spl_object_hash($connection);
            $this->handleProxyRequest($protocol, $connection, $uid);
            $this->clients[$uid] = $connection;
        } elseif ($protocol instanceof ProxyResponse) {
            $this->handleProxyResponse($protocol, $connection);
        }

    }

    protected function handleDomainRegister(DomainRegisterRequest $protocol, Connection $connection)
    {
        $this->domainMap = $this->domainMap->append(array_map(function($domain) use ($connection){
            return new DomainMapRecord($domain, $connection);
        }, $protocol->getAddingDomains()));
    }

    protected function handleProxyRequest(HttpRequest $protocol, Connection $connection, $uid)
    {
        $request = $protocol->getRequest();
        $client = $this->findProxyClient($request->getUri()->getHost());
        $proxyRequest = new ProxyRequest($request);
        $proxyRequest->setHeader('connection-id', $uid);
        $client->write($proxyRequest);
    }

    protected function handleProxyResponse(ProxyResponse $protocol, Connection $connection)
    {
        $connectionId = $protocol->getHeader('connection-id');
        if (!$connectionId || !isset($this->clients[$connectionId])) {
            $connection->write('Lose connection');
            return false;
        }
        $client = $this->clients[$connectionId];
        $client->write(Psr7\str($protocol->getResponse()));
    }

    /**
     * @param string $host
     * @return Connection
     */
    protected function findProxyClient($host)
    {
        return $this->domainMap->filter(function(DomainMapRecord $record) use ($host){
            return $record->getConnection() == $host;
        })->first()->getConnection();
    }
}