<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use \HTMLPurifier\Config;

/**
 * Implements required attribute stipulation for <script>
 */
class ScriptRequired extends AttrTransform
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
        if (!isset($attr['type'])) {
            $attr['type'] = 'text/javascript';
        }

        return $attr;
    }
}
