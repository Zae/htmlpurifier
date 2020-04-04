<?php

declare(strict_types=1);

/**
 * XHTML 1.1 Target Module, defines target attribute in link elements.
 */
class HTMLPurifier_HTMLModule_Target extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Target';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup(HTMLPurifier_Config $config): void
    {
        $elements = ['a'];
        foreach ($elements as $name) {
            $e = $this->addBlankElement($name);
            $e->attr = [
                'target' => new HTMLPurifier_AttrDef_HTML_FrameTarget()
            ];
        }
    }
}
