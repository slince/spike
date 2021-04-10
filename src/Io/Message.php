<?php


namespace Spike\Io;

class Message
{
    /** Payload flags.*/
    const PAYLOAD_NONE = 2;
    const PAYLOAD_RAW = 4;
    const PAYLOAD_ERROR = 8;
    const PAYLOAD_CONTROL = 16;

    /**
     * @var int
     */
    protected $flag;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $payload = [];

    public function __construct(string $action, array $payload = [])
    {
//        $this->flag = $flag;
        $this->action = $action;
        $this->payload = $payload;
    }

    /**
     * @return int
     */
    public function getFlag(): int
    {
        return $this->flag;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getArgument(string $name)
    {
        return $this->payload[$name] ?? null;
    }
}