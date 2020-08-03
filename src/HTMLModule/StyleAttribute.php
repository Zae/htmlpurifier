<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\AttrDef\CSS;
use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;

/**
 * XHTML 1.1 Edit Module, defines editing-related elements. Text Extension
 * Module.
 */
class StyleAttribute extends HTMLModule
{
    /**
     * @var string
     */
    public $name = 'StyleAttribute';

    /**
     * @var array
     */
    public $attr_collections = [
        // The inclusion routine differs from the Abstract Modules but
        // is in line with the DTD and XML Schemas.
        'Style' => ['style' => false], // see constructor
        'Core' => [0 => ['Style']]
    ];

    /**
     * @param Config $config
     */
    public function setup(Config $config): void
    {
        $this->attr_collections['Style']['style'] = new CSS();
    }
}
