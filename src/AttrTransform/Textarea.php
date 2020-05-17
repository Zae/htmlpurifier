<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier\Config;

/**
 * Sets height/width defaults for <textarea>
 */
class Textarea extends AttrTransform
{
    /**
     * @param array   $attr
     * @param Config  $config
     * @param Context $context
     *
     * @return array
     */
    public function transform(array $attr, Config $config, Context $context): array
    {
        // Calculated from Firefox
        if (!isset($attr['cols'])) {
            $attr['cols'] = '22';
        }

        if (!isset($attr['rows'])) {
            $attr['rows'] = '3';
        }

        return $attr;
    }
}
