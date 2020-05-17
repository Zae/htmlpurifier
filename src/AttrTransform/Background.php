<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;

/**
 * Pre-transform that changes proprietary background attribute to CSS.
 */
class Background extends AttrTransform
{
    /**
     * @param array               $attr
     * @param Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function transform(array $attr, Config $config, Context $context): array
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
