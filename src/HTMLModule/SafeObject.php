<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\AttrTransform\SafeParam;
use HTMLPurifier\HTMLModule;
use HTMLPurifier_Config;
use HTMLPurifier\Exception;

/**
 * A "safe" object module. In theory, objects permitted by this module will
 * be safe, and untrusted users can be allowed to embed arbitrary flash objects
 * (maybe other types too, but only Flash is supported as of right now).
 * Highly experimental.
 */
class SafeObject extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'SafeObject';

    /**
     * @param HTMLPurifier_Config $config
     *
     * @throws \HTMLPurifier\Exception
     */
    public function setup(HTMLPurifier_Config $config): void
    {
        // These definitions are not intrinsically safe: the attribute transforms
        // are a vital part of ensuring safety.

        $max = $config->get('HTML.MaxImgLength');
        $object = $this->addElement(
            'object',
            'Inline',
            'Optional: param | Flow | #PCDATA',
            'Common',
            [
                // While technically not required by the spec, we're forcing
                // it to this value.
                'type' => 'Enum#application/x-shockwave-flash',
                'width' => 'Pixels#' . $max,
                'height' => 'Pixels#' . $max,
                'data' => 'URI#embedded',
                'codebase' => new Enum(
                    [
                        'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0'
                    ]
                ),
            ]
        );
        $object->attr_transform_post[] = new \HTMLPurifier\AttrTransform\SafeObject();

        $param = $this->addElement(
            'param',
            false,
            'Empty',
            false,
            [
                'id' => 'ID',
                'name*' => 'Text',
                'value' => 'Text'
            ]
        );
        $param->attr_transform_post[] = new SafeParam();
        $this->info_injector[] = 'SafeObject';
    }
}
