<?php

namespace Spike;

class WorkerPool implements \IteratorAggregate, \Countable
{
    /**
     * @var Worker[]
     */
    protected $workers = [];

    public function __construct(array $workers = [])
    {
        $this->workers = $workers;
    }

    public function add(Worker $worker)
    {
        $this->workers[] = $worker;
    }

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
}