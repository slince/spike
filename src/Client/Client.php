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
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\DomainRegisterRequest;
use Spike\ProtocolFactory;
use Spike\Protocol\MessageInterface;
use Spike\Protocol\ProxyRequest;
use GuzzleHttp\Client as HttpClient;
use Spike\Protocol\ProxyResponse;

class Client
{
    protected $proxyHosts = [
        'spike.domain.com' => 'localhost:8080'
    ];

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

    protected $loop;

    public function __construct($server, LoopInterface $loop = null, HttpClient $client = null)
    {
        $this->serverAddress = $server;
        if (is_null($client)) {
            $client = new HttpClient();
        }
        $this->httpClient = $client;
        if (is_null($loop)) {
            $loop = LoopFactory::create();
        }
        $this->loop = $loop;
        $this->connector = new Connector($loop);
    }

    public function run()
    {
        $this->connector->connect($this->serverAddress)->then(function(ConnectionInterface $connection){
            $this->uploadProxyHosts($connection); //Reports the proxy hosts
            $connection->on('data', function($data) use ($connection){
                $protocol = ProtocolFactory::create($data);
                if ($protocol === false) {
                    $connection->close();
                }
                $this->acceptConnection($connection, $protocol);
            });
        });
        echo 'client running', PHP_EOL;
        $this->loop->run();
    }

    protected function acceptConnection(ConnectionInterface $connection, MessageInterface $protocol)
    {
       if ($protocol instanceof ProxyRequest) {
            var_dump('receive request');
            $forwardedConnectionId = $protocol->getHeader('Forwarded-Connection-Id');
            $request = $protocol->getRequest();
            $proxyHost = $request->getUri()->getHost() .
                ($request->getUri()->getPort() ? ":{$request->getUri()->getPort()}" : '');
            if (!isset($this->proxyHosts[$proxyHost])) {
                throw new InvalidArgumentException(sprintf('The host "%s" is not supported by the client', $proxyHost));
            }
            list($host, $port) = explode(':', $this->proxyHosts[$proxyHost]);
            $uri = $request->getUri()->withHost($host)->withPort($port);
            $request = $request->withUri($uri);
            $response = $this->httpClient->send($request);
            $connection->write(new ProxyResponse(0, $response, [
                'Forwarded-Connection-Id' => $forwardedConnectionId
            ]));
        }
    }

    protected function uploadProxyHosts(ConnectionInterface $connection)
    {
        $connection->write(new DomainRegisterRequest(array_keys($this->proxyHosts)));
    }
}