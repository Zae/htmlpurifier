<?php

declare(strict_types=1);

namespace HTMLPurifier;

/**
 * A simple array-backed queue, based off of the classic Okasaki
 * persistent amortized queue.  The basic idea is to maintain two
 * stacks: an input stack and an output stack.  When the output
 * stack runs out, reverse the input stack and use it as the output
 * stack.
 *
 * We don't use the SPL implementation because it's only supported
 * on PHP 5.3 and later.
 *
 * Exercise: Prove that push/pop on this queue take amortized O(1) time.
 *
 * Exercise: Extend this queue to be a deque, while preserving amortized
 * O(1) time.  Some care must be taken on rebalancing to avoid quadratic
 * behaviour caused by repeatedly shuffling data from the input stack
 * to the output stack and back.
 *
 * @template T
 */
class Queue
{
    /**
     * @var array
     * @psalm-var array<T>
     */
    private $input = [];

    /**
     * @var array
     * @psalm-var array<T>
     */
    private $output = [];

    /**
     * HTMLPurifier\HTMLPurifier_Queue constructor.
     *
     * @param array $input
     * @psarlm-param array<T> $input
     */
    public function __construct(array $input = [])
    {
        $this->input = $input;
        $this->output = [];
    }

    /**
     * Shifts an element off the front of the queue.
     *
     * @return mixed
     * @psalm-return T|null
     */
    public function shift()
    {
        if (empty($this->output)) {
            $this->output = array_reverse($this->input);
            $this->input = [];
        }

        if (empty($this->output)) {
            return null;
        }

        return array_pop($this->output);
    }

    /**
     * Pushes an element onto the front of the queue.
     *
     * @param mixed $x
     * @psalm-param T $x
     */
    public function push($x): void
    {
        $this->input[] = $x;
    }

    /**
     * Checks if it's empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->input) && empty($this->output);
    }
}
