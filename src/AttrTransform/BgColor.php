<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use \HTMLPurifier\Config;

/**
 * Pre-transform that changes deprecated bgcolor attribute to CSS.
 */
class BgColor extends AttrTransform
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
        if (!isset($attr['bgcolor'])) {
            return $attr;
        }

        $bgcolor = $this->confiscateAttr($attr, 'bgcolor');
        // some validation should happen here

        $this->prependCSS($attr, "background-color:$bgcolor;");

        return $attr;
    }
}
