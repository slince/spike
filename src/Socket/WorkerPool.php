<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Socket;

use React\EventLoop\LoopInterface;

abstract class WorkerPool implements \IteratorAggregate, \Countable
{
    const TYPE_FORK = 'fork';
    const TYPE_PROC = 'proc';
    const TYPE_THREAD = 'parallel';
    protected $type;

    /**
     * @var Worker[]
     */
    protected $workers = [];

    public function __construct(array $workers = [])
    {
        $this->workers = $workers;
    }

    /**
     * Add a worker to this pool.
     *
     * @param Worker $worker
     */
    public function add(Worker $worker)
    {
        $this->workers[] = $worker;
    }

    /**
     * Remove the given worker.
     *
     * @param Worker $worker
     */
    public function remove(Worker $worker)
    {
        if ($index = array_search($worker, $this->workers) !== false) {
            unset($this->workers[$index]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->workers);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->workers);
    }

    /**
     * Starts the work pool.
     */
    public function run()
    {
        foreach ($this->workers as $worker) {
            $worker->start();
        }
    }

    abstract public function createWorker(LoopInterface $loop, ServerInterface $server);
}