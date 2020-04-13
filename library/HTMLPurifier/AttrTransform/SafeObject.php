<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;

/**
 * Writes default type for all objects. Currently only supports flash.
 */
class HTMLPurifier_AttrTransform_SafeObject extends AttrTransform
{
    /**
     * @type string
     */
    public $name = 'SafeObject';

    /**
     * @param array               $attr
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function transform(array $attr, HTMLPurifier_Config $config, Context $context): array
    {
        if (!isset($attr['type'])) {
            $attr['type'] = 'application/x-shockwave-flash';
        }

        return $attr;
    }
}
