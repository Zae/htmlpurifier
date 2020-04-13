<?php

declare(strict_types=1);

use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\HTMLModule;

/**
 * A "safe" script module. No inline JS is allowed, and pointed to JS
 * files must match whitelist.
 */
class HTMLPurifier_HTMLModule_SafeScripting extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'SafeScripting';

    /**
     * @param HTMLPurifier_Config $config
     *
     * @throws HTMLPurifier_Exception
     */
    public function setup(HTMLPurifier_Config $config): void
    {
        // These definitions are not intrinsically safe: the attribute transforms
        // are a vital part of ensuring safety.

        $allowed = $config->get('HTML.SafeScripting');
        $script = $this->addElement(
            'script',
            'Inline',
            'Optional:', // Not `Empty` to not allow to autoclose the <script /> tag @see https://www.w3.org/TR/html4/interact/scripts.html
            null,
            [
                // While technically not required by the spec, we're forcing
                // it to this value.
                'type' => 'Enum#text/javascript',
                'src*' => new Enum(array_keys($allowed), /*case sensitive*/ true)
            ]
        );

        $script->attr_transform_pre[] =
        $script->attr_transform_post[] = new HTMLPurifier_AttrTransform_ScriptRequired();
    }
}
