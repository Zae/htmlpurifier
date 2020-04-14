<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier_Config;

/**
 * Pre-transform that changes converts a boolean attribute to fixed CSS
 */
class BoolToCSS extends AttrTransform
{
    /**
     * Name of boolean attribute that is trigger.
     *
     * @type string
     */
    protected $attr;

    /**
     * CSS declarations to add to style, needs trailing semicolon.
     *
     * @type string
     */
    protected $css;

    /**
     * @param string $attr attribute name to convert from
     * @param string $css  CSS declarations to add to style (needs semicolon)
     */
    public function __construct(string $attr, string $css)
    {
        $this->attr = $attr;
        $this->css = $css;
    }

    /**
     * @param array               $attr
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function transform(array $attr, HTMLPurifier_Config $config, Context $context): array
    {
        if (!isset($attr[$this->attr])) {
            return $attr;
        }

        unset($attr[$this->attr]);
        $this->prependCSS($attr, $this->css);

        return $attr;
    }
}
