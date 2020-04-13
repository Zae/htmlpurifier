<?php

declare(strict_types=1);

// this MUST be placed in post, as it assumes that any value in dir is valid
use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;

/**
 * Post-trasnform that ensures that bdo tags have the dir attribute set.
 */
class HTMLPurifier_AttrTransform_BdoDir extends AttrTransform
{
    /**
     * @param array               $attr
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return array
     * @throws HTMLPurifier_Exception
     */
    public function transform(array $attr, HTMLPurifier_Config $config, Context $context): array
    {
        if (isset($attr['dir'])) {
            return $attr;
        }
        $attr['dir'] = $config->get('Attr.DefaultTextDir');
        return $attr;
    }
}
