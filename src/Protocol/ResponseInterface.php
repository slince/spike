<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Protocol;

interface ResponseInterface
{
    /**
     * Gets the body of the protocol
     * @return string
     */
     public function getBody();

    /**
     * Parses the body
     * @param string $body
     * @return mixed
     */
    public static function parseBody($body);
}