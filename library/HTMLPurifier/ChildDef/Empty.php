<?php

declare(strict_types=1);

use HTMLPurifier\Node;

/**
 * Definition that disallows all elements.
 *
 * @warning validateChildren() in this class is actually never called, because
 *          empty elements are corrected in HTMLPurifier_Strategy_MakeWellFormed
 *          before child definitions are parsed in earnest by
 *          HTMLPurifier_Strategy_FixNesting.
 */
class HTMLPurifier_ChildDef_Empty extends HTMLPurifier_ChildDef
{
    /**
     * @type bool
     */
    public $allow_empty = true;

    /**
     * @type string
     */
    public $type = 'empty';

    /**
     * @param Node[]               $children
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return array
     */
    public function validateChildren(array $children, HTMLPurifier_Config $config, HTMLPurifier_Context $context): array
    {
        return [];
    }
}
