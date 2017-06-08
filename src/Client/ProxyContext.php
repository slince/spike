<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client;

use Spike\Client\Tunnel\TunnelInterface;

class ProxyContext
{
    /**
     * @var TunnelInterface
     */
    protected $tunnel;

    protected $arguments;

    public function __construct(TunnelInterface $tunnel, array $arguments = [])
    {
        $this->tunnel = $tunnel;
        $this->arguments = $arguments;
    }

    /**
     * @return TunnelInterface
     */
    public function getTunnel()
    {
        return $this->tunnel;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    public function getArgument($name)
    {
        return isset($this->arguments[$name]) ? $this->arguments[$name] : null;
    }
}