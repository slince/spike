<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

abstract class Response extends Protocol
{
    protected $code;

    public function __construct($code, $action, $headers = [])
    {
        $this->code = $code;
        parent::__construct($action, $headers);
    }

    public function toString()
    {
        $headers = array_merge($this->headers, [
            'Version' => static::VERSION,
            'Action' => $this->action,
            'Code' => $this->code
        ]);
        $buffer = '';
        foreach ($headers as $header) {
            $buffer .= ": {$header}\r\n";
        }
        return $buffer
            . "\r\n\r\n"
            . $this->getBody();
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }
}