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

use Spike\Version;

interface SpikeInterface
{
    /**
     * The version of protocol
     * @var string
     */
    const VERSION = Version::VERSION;

    /**
     * Creates a protocol from a string
     * @param string $string
     * @return SpikeInterface
     */
    public static function fromString($string);

    /**
     * Creates a protocol from an array
     * @param array $array
     * @return SpikeInterface
     */
    public static function fromArray($array);

    /**
     * Convert the protocol to string
     * @return string
     */
    public function toString();

    /**
     * Convert the protocol to string
     * @return string
     */
    public function __toString();

    /**
     * Sets the action of the protocol
     * @param string $action
     */
    public function setAction($action);

    /**
     * Gets action of the protocol
     * @return string
     */
    public function getAction();

    /**
     * Sets a header with given name and value
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value);

    /**
     * Sets all headers
     * @param array $headers
     */
    public function setHeaders(array $headers);

    /**
     * Gets the message header by given name
     * @param string $name
     * @return string
     */
    public function getHeader($name);

    /**
     * Gets all headers
     * @return array
     */
    public function getHeaders();

    /**
     * Sets the message body
     * @param mixed $body
     */
    public function setBody($body);

    /**
     * Gets the body of the protocol
     * @return mixed
     */
    public function getBody();
}