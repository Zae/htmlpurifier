<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;

/**
 * A "safe" embed module. See SafeObject. This is a proprietary element.
 */
class SafeEmbed extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'SafeEmbed';

    /**
     * @param Config $config
     */
    public function setup(Config $config): void
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
        $embed->attr_transform_post[] = new \HTMLPurifier\AttrTransform\SafeEmbed();
    }
}
