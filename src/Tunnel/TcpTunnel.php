<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Tunnel;

class TcpTunnel extends Tunnel
{
    /**
     * @var array
     */
    protected $host;

    public function __construct($serverPort, $host)
    {
        $this->host = $host;
        parent::__construct($serverPort);
    }

    /**
     * Gets the local host
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'protocol' => $this->getProtocol(),
            'host' => $this->host,
            'serverPort' => $this->serverPort
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocol()
    {
        return 'tcp';
    }

}