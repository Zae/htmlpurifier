<?php

declare(strict_types=1);

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;

/**
 * Class for handling width/height length attribute transformations to CSS
 */
class HTMLPurifier_AttrTransform_Length extends AttrTransform
{
    /**
     * @type string
     */
    protected $name;

    /**
     * @type string
     */
    protected $cssName;

    /**
     * HTMLPurifier_AttrTransform_Length constructor.
     *
     * @param string      $name
     * @param string|null $css_name
     */
    public function __construct(string $name, string $css_name = null)
    {
        $this->name = $name;
        $this->cssName = $css_name ?: $name;
    }

    /**
     * @param array               $attr
     * @param HTMLPurifier_Config $config
     * @param Context             $context
     *
     * @return array
     */
    public function transform(array $attr, HTMLPurifier_Config $config, Context $context): array
    {
        if (!isset($attr[$this->name])) {
            return $attr;
        }

        $length = $this->confiscateAttr($attr, $this->name);
        if (ctype_digit($length)) {
            $length .= 'px';
        }

        $this->prependCSS($attr, $this->cssName . ":$length;");

        return $attr;
    }
}
