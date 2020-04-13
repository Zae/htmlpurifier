<?php

declare(strict_types=1);

use HTMLPurifier\HTMLModule;

/**
 * Class HTMLPurifier_HTMLModule_NonXMLCommonAttributes
 */
class HTMLPurifier_HTMLModule_NonXMLCommonAttributes extends HTMLModule
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
