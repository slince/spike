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
use Monolog\Logger;
use React\EventLoop\LoopInterface;

class NonBlockStreamHandler extends StreamHandler
{
    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    public function __construct(LoopInterface $eventLoop, $stream, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false)
    {
        $this->eventLoop = $eventLoop;
        parent::__construct($stream, $level, $bubble, $filePermission, $useLocking);
    }

    /**
     * {@inheritdoc}
     */
    protected function streamWrite($stream, array $record)
    {
//        return;
        $data = (string) $record['formatted'];
        $this->eventLoop->addWriteStream($stream, function ($stream) use(&$data){
            $written = fwrite($stream, $data);
            if ($written === strlen($data)) {
                $this->eventLoop->removeWriteStream($stream);
            } else {
                $data = substr($data, $written);
            }
        });
    }
}