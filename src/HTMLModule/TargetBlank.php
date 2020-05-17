<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\HTMLModule;
use HTMLPurifier\Config;

/**
 * Module adds the target=blank attribute transformation to a tags.  It
 * is enabled by HTML.TargetBlank
 */
class TargetBlank extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'TargetBlank';

    /**
     * @param Config $config
     */
    public function setup(Config $config): void
    {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new \HTMLPurifier\AttrTransform\TargetBlank();
    }
}
