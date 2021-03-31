<?php


namespace Spike\Relay;

interface RelayInterface
{
    const BUFFER_SIZE = 65536;

    /** Payload flags.*/
    const PAYLOAD_NONE = 2;
    const PAYLOAD_RAW = 4;
    const PAYLOAD_ERROR = 8;
    const PAYLOAD_CONTROL = 16;

    public function send($payload, int $flags = null);

    public function receive(int &$flags = null);
}