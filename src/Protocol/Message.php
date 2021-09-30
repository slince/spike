<?php


namespace Spike\Protocol;

class Message
{
    public const BUFFER_SIZE = 65536;

    /** Payload flags.*/
    public const PAYLOAD_NONE    = 2;
    public const PAYLOAD_RAW     = 4;
    public const PAYLOAD_ERROR   = 8;
    public const PAYLOAD_CONTROL = 16;

    /**
     * @var int
     */
    protected $flags;

    /**
     * @var array
     */
    protected $payload = [];

    public function __construct(int $flags, array $payload = [])
    {
        $this->flags = $flags;
        $this->payload = $payload;
    }

    /**
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Pack the given message.
     * 
     * @param Message $message
     * @return string
     */
    public static function pack(Message $message): string
    {
        $flags = $message->getFlags();
        $payload = '';
        if ($message->getPayload()) {
            $payload = json_encode($message->getPayload());
        }
        $size = strlen($payload);
        $body = pack('CPJ', $flags, $size, $size);

        if (!($flags & Message::PAYLOAD_NONE)) {
            $body .= $payload;
        }
        return $body;
    }
}