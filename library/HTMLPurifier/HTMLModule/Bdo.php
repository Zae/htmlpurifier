<?php

declare(strict_types=1);

/**
 * XHTML 1.1 Bi-directional Text Module, defines elements that
 * declare directionality of content. Text Extension Module.
 */
class HTMLPurifier_HTMLModule_Bdo extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Bdo';

    /**
     * @type array
     */
    public $attr_collections = [
        'I18N' => ['dir' => false]
    ];

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup(HTMLPurifier_Config $config): void
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

        $bdo->attr_transform_post[] = new HTMLPurifier_AttrTransform_BdoDir();
        $this->attr_collections['I18N']['dir'] = 'Enum#ltr,rtl';
    }
}
