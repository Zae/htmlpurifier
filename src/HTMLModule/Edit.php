<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\ChildDef\Chameleon;
use HTMLPurifier\ElementDef;
use HTMLPurifier\HTMLModule;
use \HTMLPurifier\Config;

/**
 * XHTML 1.1 Edit Module, defines editing-related elements. Text Extension
 * Module.
 */
class Edit extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Edit';

    /**
     * @param Config $config
     */
    public function setup(Config $config): void
    {
        $contents = 'Chameleon: #PCDATA | Inline ! #PCDATA | Flow';
        $attr = [
            'cite' => 'URI',
            // 'datetime' => 'Datetime', // not implemented
        ];
        $this->addElement('del', 'Inline', $contents, 'Common', $attr);
        $this->addElement('ins', 'Inline', $contents, 'Common', $attr);
    }

    // HTML 4.01 specifies that ins/del must not contain block
    // elements when used in an inline context, chameleon is
    // a complicated workaround to acheive this effect

    // Inline context ! Block context (exclamation mark is
    // separator, see getChildDef for parsing)

    /**
     * @type bool
     */
    public $defines_child_def = true;

    /**
     * @param ElementDef $def
     *
     * @return Chameleon|null
     */
    public function getChildDef(ElementDef $def): ?Chameleon
    {
        if ($def->content_model_type !== 'chameleon') {
            return null;
        }

        if (\is_null($def->content_model)) {
            return null;
        }

        $value = explode('!', $def->content_model);

        return new Chameleon($value[0], $value[1]);
    }
}
