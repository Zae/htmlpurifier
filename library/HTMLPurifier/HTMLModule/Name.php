<?php

declare(strict_types=1);

use HTMLPurifier\HTMLModule;

/**
 * Class HTMLPurifier_HTMLModule_Name
 */
class HTMLPurifier_HTMLModule_Name extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Name';

    /**
     * @param HTMLPurifier_Config $config
     *
     * @throws HTMLPurifier_Exception
     */
    public function setup($config): void
    {
        $elements = ['a', 'applet', 'form', 'frame', 'iframe', 'img', 'map'];
        foreach ($elements as $name) {
            $element = $this->addBlankElement($name);
            $element->attr['name'] = 'CDATA';
            if (!$config->get('HTML.Attr.Name.UseCDATA')) {
                $element->attr_transform_post[] = new HTMLPurifier_AttrTransform_NameSync();
            }
        }
    }
}
