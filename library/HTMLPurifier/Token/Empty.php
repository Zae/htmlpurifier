<?php

declare(strict_types=1);

use HTMLPurifier\Node;
use HTMLPurifier\Token\Tag;

/**
 * Concrete empty token class.
 */
class HTMLPurifier_Token_Empty extends Tag
{
    public function toNode(): Node
    {
        $n = parent::toNode();
        $n->empty = true;

        return $n;
    }
}
