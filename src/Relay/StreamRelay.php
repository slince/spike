<?php


namespace Spike\Relay;

use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;
use Spike\Exception\InvalidArgumentException;
use Spike\Exception\MetaException;
use Spike\Exception\TransportException;

class StreamRelay implements RelayInterface
{
    /**
     * @var ReadableStreamInterface
     */
    protected $input;

    /**
     * @var WritableStreamInterface
     */
    protected $output;


    public function __construct($input, $output)
    {
        if (!is_resource($input) || get_resource_type($input) !== 'stream') {
            throw new InvalidArgumentException("expected a valid `in` stream resource");
        }
        if (!$this->assertReadable($input)) {
            throw new InvalidArgumentException("resource `in` must be readable");
        }
        if (!is_resource($output) || get_resource_type($output) !== 'stream') {
            throw new InvalidArgumentException("expected a valid `out` stream resource");
        }
        if (!$this->assertWritable($output)) {
            throw new InvalidArgumentException("resource `out` must be writable");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send($payload, int $flags = null)
    {
        $size = strlen($payload);
        if ($flags & self::PAYLOAD_NONE && $size != 0) {
            throw new TransportException("unable to send payload with PAYLOAD_NONE flag");
        }
        $body = pack('CPJ', $flags, $size, $size);
        if (!($flags & self::PAYLOAD_NONE)) {
            $body .= $payload;
        }
        $this->output->write($body);
    }

    /**
     * {@inheritdoc}
     */
    public function receive(int &$flags = null)
    {
        $prefix = $this->fetchMeta();
        $flags = $prefix['flags'];
        $result = null;
        if ($prefix['size'] !== 0) {
            $leftBytes = $prefix['size'];
            //Add ability to write to stream in a future
            while ($leftBytes > 0) {
                $buffer = fread($this->input, min($leftBytes, self::BUFFER_SIZE));
                if ($buffer === false) {
                    throw new TransportException("error reading payload from the stream");
                }
                $result .= $buffer;
                $leftBytes -= strlen($buffer);
            }
        }
        return $result;
    }

    /**
     * @return array Prefix [flag, length]
     *
     * @throws MetaException
     */
    private function fetchMeta(): array
    {
        $prefixBody = fread($this->input, 17);
        if ($prefixBody === false) {
            throw new MetaException("unable to read prefix from the stream");
        }
        $result = unpack("Cflags/Psize/Jrevs", $prefixBody);
        if (!is_array($result)) {
            throw new MetaException("invalid meta");
        }
        if ($result['size'] != $result['revs']) {
            throw new MetaException("invalid meta (checksum)");
        }
        return $result;
    }

    /**
     * Checks if stream is readable.
     *
     * @param resource $stream
     *
     * @return bool
     */
    private function assertReadable($stream): bool
    {
        $meta = stream_get_meta_data($stream);
        return in_array($meta['mode'], ['r', 'rb', 'r+', 'rb+', 'w+', 'wb+', 'a+', 'ab+', 'x+', 'c+', 'cb+'], true);
    }
    /**
     * Checks if stream is writable.
     *
     * @param resource $stream
     *
     * @return bool
     */
    private function assertWritable($stream): bool
    {
        $meta = stream_get_meta_data($stream);
        return $meta['mode'] !== 'r';
    }
}