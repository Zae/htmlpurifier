<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\Token\Tag;

/**
 * Defines a mutation of an obsolete tag into a valid tag.
 */
abstract class HTMLPurifier_TagTransform
{
    /**
     * Tag name to transform the tag to.
     * @type string
     */
    public $transform_to;

    /**
     * Transforms the obsolete tag into the valid tag.
     *
     * @param Tag                 $tag     Tag to be transformed.
     * @param HTMLPurifier_Config $config  Mandatory HTMLPurifier_Config object
     * @param Context             $context Mandatory HTMLPurifier\HTMLPurifier_Context object
     */
    abstract public function transform(Tag $tag, HTMLPurifier_Config $config, Context $context);

    /**
     * Prepends CSS properties to the style attribute, creating the
     * attribute if it doesn't exist.
     * @warning Copied over from AttrTransform, be sure to keep in sync
     * @param array $attr Attribute array to process (passed by reference)
     * @param string $css CSS to prepend
     */
    protected function prependCSS(array &$attr, string $css): void
    {
        $attr['style'] = $attr['style'] ?? '';
        $attr['style'] = $css . $attr['style'];
    }
}
