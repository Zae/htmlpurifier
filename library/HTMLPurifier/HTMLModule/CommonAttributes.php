<?php

declare(strict_types=1);

use HTMLPurifier\HTMLModule;

/**
 * Class HTMLPurifier_HTMLModule_CommonAttributes
 */
class HTMLPurifier_HTMLModule_CommonAttributes extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'CommonAttributes';

    /**
     * @type array
     */
    public $attr_collections = [
        'Core' => [
            0 => ['Style'],
            // 'xml:space' => false,
            'class' => 'Class',
            'id' => 'ID',
            'title' => 'CDATA',
        ],
        'Lang' => [],
        'I18N' => [
            0 => ['Lang'], // proprietary, for xml:lang/lang
        ],
        'Common' => [
            0 => ['Core', 'I18N']
        ]
    ];
}
