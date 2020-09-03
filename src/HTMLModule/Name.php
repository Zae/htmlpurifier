<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\AttrTransform\NameSync;
use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;
use HTMLPurifier\Exception;

/**
 * Class HTMLPurifier\HTMLModule\HTMLPurifier_HTMLModule_Name
 */
class Name extends HTMLModule
{
    /**
     * @var string
     */
    public $name = 'Name';

    /**
     * @param Config $config
     *
     * @throws Exception
     */
    public function setup(Config $config): void
    {
        $elements = ['a', 'applet', 'form', 'frame', 'iframe', 'img', 'map'];
        foreach ($elements as $name) {
            $element = $this->addBlankElement($name);
            $element->attr['name'] = 'CDATA';
            if (!$config->get('HTML.Attr.Name.UseCDATA')) {
                $element->attr_transform_post[] = new NameSync();
            }
        }
    }
}
