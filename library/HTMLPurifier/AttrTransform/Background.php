<?php

declare(strict_types=1);

/**
 * Pre-transform that changes proprietary background attribute to CSS.
 */
class HTMLPurifier_AttrTransform_Background extends HTMLPurifier_AttrTransform
{
    /**
     * @param array                $attr
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return array
     */
    public function transform(array $attr, HTMLPurifier_Config $config, HTMLPurifier_Context $context): array
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
