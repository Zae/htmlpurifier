<?php

declare(strict_types=1);

use HTMLPurifier\AttrDef\HTML\LinkTypes;

/**
 * XHTML 1.1 Hypertext Module, defines hypertext links. Core Module.
 */
class HTMLPurifier_HTMLModule_Hypertext extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Hypertext';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup(HTMLPurifier_Config $config): void
    {
        $a = $this->addElement(
            'a',
            'Inline',
            'Inline',
            'Common',
            [
                // 'accesskey' => 'Character',
                // 'charset' => 'Charset',
                'href' => 'URI',
                // 'hreflang' => 'LanguageCode',
                'rel' => new LinkTypes('rel'),
                'rev' => new LinkTypes('rev'),
                // 'tabindex' => 'Number',
                // 'type' => 'ContentType',
            ]
        );
        $a->formatting = true;
        $a->excludes = ['a' => true];
    }
}
