<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;

/**
 * XHTML 1.1 Object Module, defines elements for generic object inclusion
 *
 * @warning Users will commonly use <embed> to cater to legacy browsers: this
 *      module does not allow this sort of behavior
 */
class Objects extends HTMLModule
{
    /**
     * @var string
     */
    public $name = 'Objects';

    /**
     * @var bool
     */
    public $safe = false;

    /**
     * @param Config $config
     */
    public function setup(Config $config): void
    {
        $this->addElement(
            'object',
            'Inline',
            'Optional: #PCDATA | Flow | param',
            'Common',
            [
                'archive' => 'URI',
                'classid' => 'URI',
                'codebase' => 'URI',
                'codetype' => 'Text',
                'data' => 'URI',
                'declare' => 'Bool#declare',
                'height' => 'Length',
                'name' => 'CDATA',
                'standby' => 'Text',
                'tabindex' => 'Number',
                'type' => 'ContentType',
                'width' => 'Length'
            ]
        );

        $this->addElement(
            'param',
            null,
            'Empty',
            null,
            [
                'id' => 'ID',
                'name*' => 'Text',
                'type' => 'Text',
                'value' => 'Text',
                'valuetype' => 'Enum#data,ref,object'
            ]
        );
    }
}
