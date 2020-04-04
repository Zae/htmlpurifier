<?php

declare(strict_types=1);

/**
 * Pre-transform that changes deprecated name attribute to ID if necessary
 */
class HTMLPurifier_AttrTransform_Name extends HTMLPurifier_AttrTransform
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
        // Abort early if we're using relaxed definition of name
        if ($config->get('HTML.Attr.Name.UseCDATA')) {
            return $attr;
        }

        if (!isset($attr['name'])) {
            return $attr;
        }

        $id = $this->confiscateAttr($attr, 'name');
        if (isset($attr['id'])) {
            return $attr;
        }

        $attr['id'] = $id;

        return $attr;
    }
}
