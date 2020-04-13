<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;

/**
 * Pre-transform that changes deprecated bgcolor attribute to CSS.
 */
class HTMLPurifier_AttrTransform_BgColor extends AttrTransform
{
    /**
     * @param array               $attr
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function transform(array $attr, HTMLPurifier_Config $config, Context $context): array
    {
        if (!isset($attr['bgcolor'])) {
            return $attr;
        }

        $bgcolor = $this->confiscateAttr($attr, 'bgcolor');
        // some validation should happen here

        $this->prependCSS($attr, "background-color:$bgcolor;");
        return $attr;
    }
}
