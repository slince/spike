<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Parser;

interface ParserInterface
{
    /**
     * Push incoming data to the parser
     * @param string $data
     */
    public function pushIncoming($data);

    /**
     * Parse the data to messages
     * @return array
     */
    public function parse();

    /**
     * Parse one message from the data
     * @return string
     */
    public function parseFirst();

    /**
     * Get the reset of data
     * @return string
     */
    public function getRestData();
}