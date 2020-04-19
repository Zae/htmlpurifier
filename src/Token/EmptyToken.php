<?php

declare(strict_types=1);

namespace HTMLPurifier\Token;

use HTMLPurifier\Node;
use HTMLPurifier\Token\Tag;

/**
 * Concrete empty token class.
 */
class EmptyToken extends Tag
{
    public function toNode(): Node
    {
        $n = parent::toNode();
        $n->empty = true;

        return $n;
    }
}
