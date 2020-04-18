<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\HTMLModule;

/**
 * Class HTMLPurifier\HTMLModule\HTMLPurifier_HTMLModule_NonXMLCommonAttributes
 */
class NonXMLCommonAttributes extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'NonXMLCommonAttributes';

    /**
     * @type array
     */
    public $attr_collections = [
        'Lang' => [
            'lang' => 'LanguageCode',
        ]
    ];
}
