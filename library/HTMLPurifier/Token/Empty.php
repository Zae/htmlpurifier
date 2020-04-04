<?php

declare(strict_types=1);

/**
 * Concrete empty token class.
 */
class HTMLPurifier_Token_Empty extends HTMLPurifier_Token_Tag
{
    public function toNode(): HTMLPurifier_Node
    {
        $n = parent::toNode();
        $n->empty = true;

        return $n;
    }
}
