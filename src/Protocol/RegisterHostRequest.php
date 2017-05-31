<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

class RegisterHostRequest extends Request
{
    protected $domains;

    public function __construct(array $domains, $headers = [])
    {
        $this->domains = $domains;
        parent::__construct('register_domain', $headers);
    }

    public function getAddingDomains()
    {
        return $this->domains;
    }

    public function getBody()
    {
        return serialize($this->domains);
    }

    public static function parseBody($body)
    {
        return unserialize($body);
    }
}