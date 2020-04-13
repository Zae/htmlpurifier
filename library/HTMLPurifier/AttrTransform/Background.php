<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;

/**
 * Pre-transform that changes proprietary background attribute to CSS.
 */
class HTMLPurifier_AttrTransform_Background extends AttrTransform
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
        if (!isset($attr['background'])) {
            return $attr;
        }

        $background = $this->confiscateAttr($attr, 'background');
        // some validation should happen here

        $this->prependCSS($attr, "background-image:url($background);");

        return $attr;
    }
}
