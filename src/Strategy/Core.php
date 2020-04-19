<?php

declare(strict_types=1);

namespace HTMLPurifier\Strategy;

use HTMLPurifier\Strategy\FixNesting;
use HTMLPurifier\Strategy\MakeWellFormed;
use HTMLPurifier\Strategy\RemoveForeignElements;
use HTMLPurifier\Strategy\ValidateAttributes;

/**
 * Core strategy composed of the big four strategies.
 */
class Core extends Composite
{
    public function __construct()
    {
        $this->strategies[] = new RemoveForeignElements();
        $this->strategies[] = new MakeWellFormed();
        $this->strategies[] = new FixNesting();
        $this->strategies[] = new ValidateAttributes();
    }
}
