<?php

declare(strict_types=1);

/**
 * Class HTMLPurifier_AttrTransform_SafeEmbed
 */
class HTMLPurifier_AttrTransform_SafeEmbed extends HTMLPurifier_AttrTransform
{
    /**
     * @type string
     */
    public $name = 'SafeEmbed';

    /**
     * @param array                $attr
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return array
     */
    public function transform(array $attr, HTMLPurifier_Config $config, HTMLPurifier_Context $context): array
    {
        $attr['allowscriptaccess'] = 'never';
        $attr['allownetworking'] = 'internal';
        $attr['type'] = 'application/x-shockwave-flash';

        return $attr;
    }
}
