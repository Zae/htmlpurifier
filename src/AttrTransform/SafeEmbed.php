<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier\Config;

/**
 * Class HTMLPurifier\AttrTransform\HTMLPurifier_AttrTransform_SafeEmbed
 */
class SafeEmbed extends AttrTransform
{
    /**
     * @type string
     */
    public $name = 'SafeEmbed';

    /**
     * @param array   $attr
     * @param Config  $config
     * @param Context $context
     *
     * @return array
     */
    public function transform(array $attr, Config $config, Context $context): array
    {
        $attr['allowscriptaccess'] = 'never';
        $attr['allownetworking'] = 'internal';
        $attr['type'] = 'application/x-shockwave-flash';

        return $attr;
    }
}
