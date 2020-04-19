<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\AttrDef\CSS;
use HTMLPurifier\HTMLModule;
use \HTMLPurifier\Config;

/**
 * XHTML 1.1 Edit Module, defines editing-related elements. Text Extension
 * Module.
 */
class StyleAttribute extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'StyleAttribute';

    /**
     * @type array
     */
    public $attr_collections = [
        // The inclusion routine differs from the Abstract Modules but
        // is in line with the DTD and XML Schemas.
        'Style' => ['style' => false], // see constructor
        'Core' => [0 => ['Style']]
    ];

    /**
     * @param \HTMLPurifier\Config $config
     */
    public function setup(\HTMLPurifier\Config $config): void
    {
        $this->attr_collections['Style']['style'] = new CSS();
    }
}
