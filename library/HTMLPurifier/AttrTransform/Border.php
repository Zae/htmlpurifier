<?php

declare(strict_types=1);

/**
 * Pre-transform that changes deprecated border attribute to CSS.
 */
class HTMLPurifier_AttrTransform_Border extends HTMLPurifier_AttrTransform
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
        if (!isset($attr['border'])) {
            return $attr;
        }

        $border_width = $this->confiscateAttr($attr, 'border');

        // some validation should happen here
        $this->prependCSS($attr, "border:{$border_width}px solid;");

        return $attr;
    }
}
