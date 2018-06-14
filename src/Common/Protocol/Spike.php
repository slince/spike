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

use Spike\Common\Exception\BadRequestException;

class Spike implements SpikeInterface
{
    /**
     * The action.
     *
     * @var string
     */
    protected $action;

    /**
     * Array of custom headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * @var mixed
     */
    protected $body;

    /**
     * The global headers.
     *
     * @var array
     */
    protected static $globalHeaders = [];

    /**
     * Spike constructor.
     *
     * @param string $action
     * @param mixed  $body
     * @param array  $headers
     */
    public function __construct($action, $body = null, $headers = [])
    {
        $this->action = $action;
        $this->body = $body;
        $this->headers = $headers;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        $data = [
            'action' => $this->action,
            'headers' => array_merge($this->headers, static::$globalHeaders),
            'body' => $this->body,
        ];

        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string)
    {
        $parsed = json_decode($string, true);
        if (json_last_error() || !isset($headers['action'])) {
            throw new BadRequestException('Bad spike protocol message"');
        }

        return new static($parsed['action'], $parsed['body'], $parsed['headers']);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromArray($array)
    {
        if (!isset($array['action'])) {
            throw new BadRequestException('Bad spike protocol message"');
        }

        return new static($array['action'], $array['body'], $array['headers']);
    }

    /**
     * Sets a global header.
     *
     * @param string $name
     * @param string $value
     */
    public static function setGlobalHeader($name, $value)
    {
        static::$globalHeaders[$name] = $value;
    }
}