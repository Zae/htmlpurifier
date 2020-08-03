<?php

declare(strict_types=1);

namespace HTMLPurifier\ChildDef;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\ChildDef;
use HTMLPurifier\Node;

/**
 * Definition that disallows all elements.
 *
 * @warning validateChildren() in this class is actually never called, because
 *          empty elements are corrected in HTMLPurifier\Strategy\HTMLPurifier_Strategy_MakeWellFormed
 *          before child definitions are parsed in earnest by
 *          HTMLPurifier\Strategy\HTMLPurifier_Strategy_FixNesting.
 */
class Nothing extends ChildDef
{
    /**
     * @var bool
     */
    public $allow_empty = true;

    /**
     * @var string
     */
    public $type = 'empty';

    /**
     * @param Node[]              $children
     * @param Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function validateChildren(array $children, Config $config, Context $context): array
    {
        return [];
    }
}
