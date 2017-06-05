<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

class ReportClientException extends SpikeRequest
{
    protected $exception;

    public function __construct($exception, array $headers = [])
    {
        $this->exception = $exception;
        parent::__construct('report_client_exception', $headers);
    }

    public function getBody()
    {
        return serialize($this->exception);
    }

    public static function parseBody($body)
    {
        return unserialize($body);
    }

    /**
     * @return string
     */
    public function getException()
    {
        return $this->exception;
    }
}