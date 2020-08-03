<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\HTMLModule;

/**
 * Class CommonAttributes
 *
 * @package HTMLPurifier\HTMLModule
 */
class CommonAttributes extends HTMLModule
{
    /**
     * @var string
     */
    public $name = 'CommonAttributes';

    /**
     * @var array
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
