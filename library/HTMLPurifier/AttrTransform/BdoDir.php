<?php

declare(strict_types=1);

// this MUST be placed in post, as it assumes that any value in dir is valid

/**
 * Post-trasnform that ensures that bdo tags have the dir attribute set.
 */
class HTMLPurifier_AttrTransform_BdoDir extends HTMLPurifier_AttrTransform
{
    /**
     * @param array                $attr
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return array
     * @throws HTMLPurifier_Exception
     */
    public function transform(array $attr, HTMLPurifier_Config $config, HTMLPurifier_Context $context): array
    {
        if (isset($attr['dir'])) {
            return $attr;
        }
        $attr['dir'] = $config->get('Attr.DefaultTextDir');
        return $attr;
    }
}
