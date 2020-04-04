<?php

declare(strict_types=1);

/**
 * Class HTMLPurifier_HTMLModule_XMLCommonAttributes
 */
class HTMLPurifier_HTMLModule_XMLCommonAttributes extends HTMLPurifier_HTMLModule
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
