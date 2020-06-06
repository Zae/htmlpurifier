<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;

/**
 * Writes default type for all objects. Currently only supports flash.
 */
class SafeObject extends AttrTransform
{
    /**
     * @type string
     */
    public $name = 'SafeObject';

    /**
     * @param array   $attr
     * @param Config  $config
     * @param Context $context
     *
     * @return array
     */
    public function transform(array $attr, Config $config, Context $context): array
    {
        if (!isset($attr['type'])) {
            $attr['type'] = 'application/x-shockwave-flash';
        }

        return $attr;
    }
}
