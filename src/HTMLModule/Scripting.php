<?php

declare(strict_types=1);

/*

WARNING: THIS MODULE IS EXTREMELY DANGEROUS AS IT ENABLES INLINE SCRIPTING
INSIDE HTML PURIFIER DOCUMENTS. USE ONLY WITH TRUSTED USER INPUT!!!

*/

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\AttrDef\URI;
use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\AttrTransform\ScriptRequired;
use HTMLPurifier\ElementDef;
use HTMLPurifier\HTMLModule;
use \HTMLPurifier\Config;

/**
 * XHTML 1.1 Scripting module, defines elements that are used to contain
 * information pertaining to executable scripts or the lack of support
 * for executable scripts.
 *
 * @note This module does not contain inline scripting elements
 */
class Scripting extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Scripting';

    /**
     * @type array
     */
    public $elements = ['script', 'noscript'];

    /**
     * @type array
     */
    public $content_sets = ['Block' => 'script | noscript', 'Inline' => 'script | noscript'];

    /**
     * @type bool
     */
    public $safe = false;

    /**
     * @param \HTMLPurifier\Config $config
     */
    public function setup(\HTMLPurifier\Config $config): void
    {
        // TODO: create custom child-definition for noscript that
        // auto-wraps stray #PCDATA in a similar manner to
        // blockquote's custom definition (we would use it but
        // blockquote's contents are optional while noscript's contents
        // are required)

        // TODO: convert this to new syntax, main problem is getting
        // both content sets working

        // In theory, this could be safe, but I don't see any reason to
        // allow it.
        $this->info['noscript'] = new ElementDef();
        $this->info['noscript']->attr = [0 => ['Common']];
        $this->info['noscript']->content_model = 'Heading | List | Block';
        $this->info['noscript']->content_model_type = 'required';

        $this->info['script'] = new ElementDef();
        $this->info['script']->attr = [
            'defer' => new Enum(['defer']),
            'src' => new URI(true),
            'type' => new Enum(['text/javascript'])
        ];
        $this->info['script']->content_model = '#PCDATA';
        $this->info['script']->content_model_type = 'optional';
        $this->info['script']->attr_transform_pre[] =
        $this->info['script']->attr_transform_post[] =
            new ScriptRequired();
    }
}
