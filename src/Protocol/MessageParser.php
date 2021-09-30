<?php


namespace Spike\Protocol;

use Evenement\EventEmitter;
use React\Stream\DuplexStreamInterface;
use Spike\Exception\MetaException;

final class MessageParser extends EventEmitter
{
    public function handle(DuplexStreamInterface $connection)
    {
        $buffer = '';
        $readSize = 0;
        $meta = null;
        $connection->on('data', function($data) use(&$buffer, &$readSize, &$meta, $connection){
            $buffer .= $data;
            $readSize += strlen($data);
            if (null === $meta && $readSize >= 17) {
                $meta = $this->parseMeta(substr($buffer, 0, 17));
                $this->emit('meta', $meta);
                $buffer = substr($buffer, 17); // reset buffer
                $readSize = strlen($buffer);
            }
            if (null !== $meta && $readSize >= $meta['size']) {
                $body = substr($buffer, 0, $meta['size']);
                $payload = $this->parsePayload($body);
                $message = new Message($meta['flags'], $payload['action'], $payload['payload']);
                $this->emit('message', [$message, $connection, $meta]);
                $buffer = substr($buffer, $meta['size']); // reset buffer
                $readSize = strlen($buffer);
                $meta = null;
            }
        });
    }

    protected function parsePayload(string $payload)
    {
        return \json_decode($payload, true) ?: [];
    }

    protected function parseMeta(string $data)
    {
        $result = unpack("Cflags/Psize/Jrevs", $data);
        if (!is_array($result)) {
            throw new MetaException("invalid meta");
        }
        if ($result['size'] != $result['revs']) {
            throw new MetaException("invalid meta (checksum)");
        }
        return $result;
    }
}