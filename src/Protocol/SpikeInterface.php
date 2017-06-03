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
     * Gets the body of the protocol
     * @return string
     */
     public function getBody();

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
     * Parses the body
     * @param string $body
     * @return mixed
     */
    public static function parseBody($body);

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
}