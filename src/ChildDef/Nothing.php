<?php

declare(strict_types=1);

namespace HTMLPurifier\ChildDef;

use HTMLPurifier\Context;
use HTMLPurifier\ChildDef;
use HTMLPurifier\Node;
use HTMLPurifier_Config;

/**
 * Definition that disallows all elements.
 *
 * @warning validateChildren() in this class is actually never called, because
 *          empty elements are corrected in HTMLPurifier_Strategy_MakeWellFormed
 *          before child definitions are parsed in earnest by
 *          HTMLPurifier_Strategy_FixNesting.
 */
class Nothing extends ChildDef
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
     * @param Node[]              $children
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function validateChildren(array $children, HTMLPurifier_Config $config, Context $context): array
    {
        return [];
    }
}
