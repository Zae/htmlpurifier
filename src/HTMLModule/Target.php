<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\AttrDef\HTML\FrameTarget;
use HTMLPurifier\HTMLModule;
use \HTMLPurifier\Config;

/**
 * XHTML 1.1 Target Module, defines target attribute in link elements.
 */
class Target extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Target';

    /**
     * @param \HTMLPurifier\Config $config
     */
    public function setup(\HTMLPurifier\Config $config): void
    {
        $elements = ['a'];
        foreach ($elements as $name) {
            $e = $this->addBlankElement($name);
            $e->attr = [
                'target' => new FrameTarget()
            ];
        }
    }
}
