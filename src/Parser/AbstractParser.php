<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Parser;

abstract class AbstractParser implements ParserInterface
{
    /**
     * The incoming buffer
     * @var string
     */
    protected $incomingData;

    /**
     * {@inheritdoc}
     */
    public function pushIncoming($data)
    {
        $this->incomingData .= $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getRestData()
    {
        return $this->incomingData;
    }
}