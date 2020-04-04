<?php

declare(strict_types=1);

/**
 * A "safe" embed module. See SafeObject. This is a proprietary element.
 */
class HTMLPurifier_HTMLModule_SafeEmbed extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'SafeEmbed';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup(HTMLPurifier_Config $config): void
    {
        $max = $config->get('HTML.MaxImgLength');
        $embed = $this->addElement(
            'embed',
            'Inline',
            'Empty',
            'Common',
            [
                'src*' => 'URI#embedded',
                'type' => 'Enum#application/x-shockwave-flash',
                'width' => 'Pixels#' . $max,
                'height' => 'Pixels#' . $max,
                'allowscriptaccess' => 'Enum#never',
                'allownetworking' => 'Enum#internal',
                'flashvars' => 'Text',
                'wmode' => 'Enum#window,transparent,opaque',
                'name' => 'ID',
            ]
        );
        $embed->attr_transform_post[] = new HTMLPurifier_AttrTransform_SafeEmbed();
    }
}
