<?php

declare(strict_types=1);

namespace HTMLPurifier;

use \HTMLPurifier\Config;

/**
 * Defines allowed child nodes and validates nodes against it.
 */
abstract class ChildDef
{
    /**
     * Type of child definition, usually right-most part of class name lowercase.
     * Used occasionally in terms of context.
     *
     * @type string
     */
    public $type;

    /**
     * Indicates whether or not an empty array of children is okay.
     *
     * This is necessary for redundant checking when changes affecting
     * a child node may cause a parent node to now be disallowed.
     *
     * @type bool
     */
    public $allow_empty;

    /**
     * Lookup array of all elements that this definition could possibly allow.
     *
     * @type array
     */
    public $elements = [];

    /**
     * Get lookup of tag names that should not close this element automatically.
     * All other elements will do so.
     *
     * @param Config $config \HTMLPurifier\Config object
     *
     * @return array
     */
    public function getAllowedElements(Config $config)
    {
        return $this->elements;
    }

    /**
     * Validates nodes according to definition and returns modification.
     *
     * @param Node[]  $children Array of HTMLPurifier\HTMLPurifier_Node
     * @param Config  $config   \HTMLPurifier\Config object
     * @param Context $context  HTMLPurifier\HTMLPurifier_Context object
     *
     * @return bool|array true to leave nodes as is, false to remove parent node, array of replacement children
     */
    abstract public function validateChildren(array $children, Config $config, Context $context);
}
