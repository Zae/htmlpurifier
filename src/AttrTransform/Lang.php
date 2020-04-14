<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier_Config;

/**
 * Post-transform that copies lang's value to xml:lang (and vice-versa)
 *
 * @note Theoretically speaking, this could be a pre-transform, but putting
 *       post is more efficient.
 */
class Lang extends AttrTransform
{
    /**
     * @param array               $attr
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function transform(array $attr, HTMLPurifier_Config $config, Context $context): array
    {
        $lang = $attr['lang'] ?? false;
        $xml_lang = $attr['xml:lang'] ?? false;

        if ($lang !== false && $xml_lang === false) {
            $attr['xml:lang'] = $lang;
        } elseif ($xml_lang !== false) {
            $attr['lang'] = $xml_lang;
        }

        return $attr;
    }
}
