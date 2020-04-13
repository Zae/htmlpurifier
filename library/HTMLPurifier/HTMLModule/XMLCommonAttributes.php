<?php

declare(strict_types=1);

use HTMLPurifier\HTMLModule;

/**
 * Class HTMLPurifier_HTMLModule_XMLCommonAttributes
 */
class HTMLPurifier_HTMLModule_XMLCommonAttributes extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'XMLCommonAttributes';

    /**
     * @type array
     */
    public $attr_collections = [
        'Lang' => [
            'xml:lang' => 'LanguageCode',
        ]
    ];
}
