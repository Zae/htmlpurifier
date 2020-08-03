<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\AttrTransform\ScriptRequired;
use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;
use HTMLPurifier\Exception;

/**
 * A "safe" script module. No inline JS is allowed, and pointed to JS
 * files must match whitelist.
 */
class SafeScripting extends HTMLModule
{
    /**
     * @var string
     */
    public $name = 'SafeScripting';

    /**
     * @param Config $config
     *
     * @throws Exception
     */
    public function setup(Config $config): void
    {
        // These definitions are not intrinsically safe: the attribute transforms
        // are a vital part of ensuring safety.

        $allowed = $config->get('HTML.SafeScripting');
        $script = $this->addElement(
            'script',
            'Inline',
            'Optional:', // Not `Empty` to not allow to autoclose the <script /> tag
            // @see https://www.w3.org/TR/html4/interact/scripts.html
            null,
            [
                // While technically not required by the spec, we're forcing
                // it to this value.
                'type' => 'Enum#text/javascript',
                'src*' => new Enum(array_keys($allowed), /*case sensitive*/ true)
            ]
        );

        $script->attr_transform_pre[] =
        $script->attr_transform_post[] = new ScriptRequired();
    }
}
