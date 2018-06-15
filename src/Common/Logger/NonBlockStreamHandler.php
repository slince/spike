<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Common\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as Monologer;
use React\EventLoop\LoopInterface;
use React\Stream\WritableResourceStream;

class NonBlockStreamHandler extends StreamHandler
{
    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * @var WritableResourceStream
     */
    protected $nonBlockStream;

    public function __construct(LoopInterface $eventLoop, $stream, $level = Monologer::DEBUG, $bubble = true, $filePermission = null, $useLocking = false)
    {
        $this->eventLoop = $eventLoop;
        parent::__construct($stream, $level, $bubble, $filePermission, $useLocking);
    }

    /**
     * {@inheritdoc}
     */
    protected function streamWrite($stream, array $record)
    {
        try{
            if ($this->nonBlockStream === null) {
                $this->nonBlockStream = new WritableResourceStream($stream, $this->eventLoop);
            }
            $this->nonBlockStream->write((string) $record['formatted']);
        } catch (\RuntimeException $exception) {
            //Polyfill
            parent::streamWrite($stream, $record);
        }
    }
}