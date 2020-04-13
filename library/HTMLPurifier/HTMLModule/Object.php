<?php

declare(strict_types=1);

use HTMLPurifier\HTMLModule;

/**
 * XHTML 1.1 Object Module, defines elements for generic object inclusion
 *
 * @warning Users will commonly use <embed> to cater to legacy browsers: this
 *      module does not allow this sort of behavior
 */
class HTMLPurifier_HTMLModule_Object extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Object';

    /**
     * @type bool
     */
    public $safe = false;

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup(HTMLPurifier_Config $config): void
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
            false,
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
