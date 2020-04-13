<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\ChildDef;
use HTMLPurifier\Node;

/**
 * Definition that uses different definitions depending on context.
 *
 * The del and ins tags are notable because they allow different types of
 * elements depending on whether or not they're in a block or inline context.
 * Chameleon allows this behavior to happen by using two different
 * definitions depending on context.  While this somewhat generalized,
 * it is specifically intended for those two tags.
 */
class HTMLPurifier_ChildDef_Chameleon extends ChildDef
{
    /**
     * Instance of the definition object to use when inline. Usually stricter.
     *
     * @type HTMLPurifier_ChildDef_Optional
     */
    public $inline;

    /**
     * Instance of the definition object to use when block.
     *
     * @type HTMLPurifier_ChildDef_Optional
     */
    public $block;

    /**
     * @type string
     */
    public $type = 'chameleon';

    /**
     * @param array $inline List of elements to allow when inline.
     * @param array $block  List of elements to allow when block.
     */
    public function __construct($inline, $block)
    {
        $this->inline = new HTMLPurifier_ChildDef_Optional($inline);
        $this->block = new HTMLPurifier_ChildDef_Optional($block);

        $this->elements = $this->block->elements;
    }

    /**
     * @param Node[]              $children
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return bool
     */
    public function validateChildren(array $children, HTMLPurifier_Config $config, Context $context)
    {
        if ($context->get('IsInline') === false) {
            return $this->block->validateChildren(
                $children,
                $config,
                $context
            );
        }

        return $this->inline->validateChildren(
            $children,
            $config,
            $context
        );
    }
}
