<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Common\Protocol;

/**
 * {@link https://github.com/clue/php-json-stream/blob/master/src/StreamingJsonParser.php}.
 */
class StreamingJsonParser
{
    private $buffer = '';
    private $endCharacter = null;

    private $assoc = true;

    public function push($chunk)
    {
        $objects = array();

        while ('' !== $chunk) {
            if (null === $this->endCharacter) {
                // trim leading whitespace
                $chunk = ltrim($chunk);

                if ('' === $chunk) {
                    // only whitespace => skip chunk
                    break;
                } elseif ('[' === $chunk[0]) {
                    // array/list delimiter
                    $this->endCharacter = ']';
                } elseif ('{' === $chunk[0]) {
                    // object/hash delimiter
                    $this->endCharacter = '}';
                }
            }

            $pos = strpos($chunk, $this->endCharacter);

            // no end found in chunk => must be part of segment, wait for next chunk
            if (false === $pos) {
                $this->buffer .= $chunk;
                break;
            }

            // possible end found in chunk => select possible segment from buffer, keep remaining chunk
            $this->buffer .= substr($chunk, 0, $pos + 1);
            $chunk = substr($chunk, $pos + 1);

            // try to parse
            $json = json_decode($this->buffer, $this->assoc);

            // successfully parsed
            if (null !== $json) {
                $objects[] = $json;

                // clear parsed buffer and continue checking remaining chunk
                $this->buffer = '';
                $this->endCharacter = null;
            }
        }

        return $objects;
    }

    public function isEmpty()
    {
        return '' === $this->buffer;
    }

    /**
     * Get the reset of data.
     *
     * @return string
     */
    public function getRemainingChunk()
    {
        return $this->buffer;
    }
}
