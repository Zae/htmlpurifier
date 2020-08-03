<?php

declare(strict_types=1);

namespace HTMLPurifier;

use FilterIterator;
use Iterator;

/**
 * Property list iterator. Do not instantiate this class directly.
 */
class PropertyListIterator extends FilterIterator
{
    /**
     * @var int
     */
    protected $l;

    /**
     * @var string
     */
    protected $filter;

    /**
     * @param Iterator $iterator Array of data to iterate over
     * @param string   $filter   Optional prefix to only allow values of
     */
    public function __construct(Iterator $iterator, string $filter)
    {
        parent::__construct($iterator);

        $this->l = \strlen($filter);
        $this->filter = $filter;
    }

    /**
     * @return bool
     */
    public function accept(): bool
    {
        $key = $this->getInnerIterator()->key();

        if (strncmp($key, $this->filter, $this->l) !== 0) {
            return false;
        }

        return true;
    }
}
