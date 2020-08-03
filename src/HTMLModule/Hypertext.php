<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\AttrDef\HTML\LinkTypes;
use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;

/**
 * XHTML 1.1 Hypertext Module, defines hypertext links. Core Module.
 */
class Hypertext extends HTMLModule
{
    /**
     * @var string
     */
    public $name = 'Hypertext';

    /**
     * @param Config $config
     */
    public function setup(Config $config): void
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
