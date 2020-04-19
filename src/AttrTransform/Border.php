<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use \HTMLPurifier\Config;

/**
 * Pre-transform that changes deprecated border attribute to CSS.
 */
class Border extends AttrTransform
{
    /**
     * @param array               $attr
     * @param \HTMLPurifier\Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function transform(array $attr, \HTMLPurifier\Config $config, Context $context): array
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
