<?php

declare(strict_types=1);

/**
 * Writes default type for all objects. Currently only supports flash.
 */
class HTMLPurifier_AttrTransform_SafeObject extends HTMLPurifier_AttrTransform
{
    /**
     * @type string
     */
    public $name = 'SafeObject';

    /**
     * @param array                $attr
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return array
     */
    public function transform(array $attr, HTMLPurifier_Config $config, HTMLPurifier_Context $context): array
    {
        if (!isset($attr['type'])) {
            $attr['type'] = 'application/x-shockwave-flash';
        }

        return $attr;
    }
}
