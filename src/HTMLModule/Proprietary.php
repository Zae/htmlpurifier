<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\HTMLModule;
use \HTMLPurifier\Config;

/**
 * Module defines proprietary tags and attributes in HTML.
 *
 * @warning If this module is enabled, standards-compliance is off!
 */
class Proprietary extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Proprietary';

    /**
     * @param \HTMLPurifier\Config $config
     */
    public function setup(\HTMLPurifier\Config $config): void
    {
        $this->addElement(
            'marquee',
            'Inline',
            'Flow',
            'Common',
            [
                'direction' => 'Enum#left,right,up,down',
                'behavior' => 'Enum#alternate',
                'width' => 'Length',
                'height' => 'Length',
                'scrolldelay' => 'Number',
                'scrollamount' => 'Number',
                'loop' => 'Number',
                'bgcolor' => 'Color',
                'hspace' => 'Pixels',
                'vspace' => 'Pixels',
            ]
        );
    }
}
