<?php

declare(strict_types=1);

namespace HTMLPurifier\AttrTransform;

use HTMLPurifier\Context;
use HTMLPurifier\AttrTransform;
use HTMLPurifier\Config;

/**
 * Class for handling width/height length attribute transformations to CSS
 */
class Length extends AttrTransform
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $cssName;

    /**
     * HTMLPurifier\AttrTransform\HTMLPurifier_AttrTransform_Length constructor.
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
     * @param array   $attr
     * @param Config  $config
     * @param Context $context
     *
     * @return array
     */
    public function transform(array $attr, Config $config, Context $context): array
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
