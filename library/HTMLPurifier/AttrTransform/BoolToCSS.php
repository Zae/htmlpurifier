<?php

declare(strict_types=1);

/**
 * Pre-transform that changes converts a boolean attribute to fixed CSS
 */
class HTMLPurifier_AttrTransform_BoolToCSS extends HTMLPurifier_AttrTransform
{
    /**
     * Name of boolean attribute that is trigger.
     *
     * @type string
     */
    protected $attr;

    /**
     * CSS declarations to add to style, needs trailing semicolon.
     *
     * @type string
     */
    protected $css;

    /**
     * @param string $attr attribute name to convert from
     * @param string $css  CSS declarations to add to style (needs semicolon)
     */
    public function __construct(string $attr, string $css)
    {
        $this->attr = $attr;
        $this->css = $css;
    }

    /**
     * @param array                $attr
     * @param HTMLPurifier_Config  $config
     * @param HTMLPurifier_Context $context
     *
     * @return array
     */
    public function transform(array $attr, HTMLPurifier_Config $config, HTMLPurifier_Context $context): array
    {
        if (!isset($attr[$this->attr])) {
            return $attr;
        }

        unset($attr[$this->attr]);
        $this->prependCSS($attr, $this->css);

        return $attr;
    }
}
