<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

interface SpikeInterface extends MessageInterface
{
    /**
     * The version of protocol
     * @var string
     */
    const VERSION = 1.0;

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
     * @return string
     */
    public function getBody();

    /**
     * Serialize the message body
     * @param mixed $body
     * @return string
     */
    public static function serializeBody($body);

    /**
     * Unserialize the message body
     * @param string $rawBody
     * @return mixed
     */
    public static function unserializeBody($rawBody);
}