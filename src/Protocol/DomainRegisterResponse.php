<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

class DomainRegisterResponse extends Response
{
    protected $body;

    public function __construct($code, $body = '', $headers = [])
    {
        $this->body = $body;
        parent::__construct($code,'domain_register_response', $headers);
    }

    public function getBody()
    {
        return $this->body;
    }

    public function parseBody($body)
    {
        $this->body = $body;
    }
}