<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\HTMLModule;

/**
 * Class HTMLPurifier\HTMLModule\HTMLPurifier_HTMLModule_XMLCommonAttributes
 */
class XMLCommonAttributes extends HTMLModule
{
    /**
     * @var string
     */
    public $name = 'XMLCommonAttributes';

    /**
     * @var array
     */
    public $attr_collections = [
        'Lang' => [
            'xml:lang' => 'LanguageCode',
        ]
    ];
}
