<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\AttrTransform\BdoDir;
use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;

/**
 * XHTML 1.1 Bi-directional Text Module, defines elements that
 * declare directionality of content. Text Extension Module.
 */
class Bdo extends HTMLModule
{
    /**
     * @var string
     */
    public $name = 'Bdo';

    /**
     * @var array
     */
    public $attr_collections = [
        'I18N' => ['dir' => false]
    ];

    /**
     * @param Config $config
     */
    public function setup(Config $config): void
    {
        $bdo = $this->addElement(
            'bdo',
            'Inline',
            'Inline',
            ['Core', 'Lang'],
            [
                'dir' => 'Enum#ltr,rtl', // required
                // The Abstract Module specification has the attribute
                // inclusions wrong for bdo: bdo allows Lang
            ]
        );

        $bdo->attr_transform_post[] = new BdoDir();
        $this->attr_collections['I18N']['dir'] = 'Enum#ltr,rtl';
    }
}
