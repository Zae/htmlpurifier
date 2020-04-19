<?php

declare(strict_types=1);

namespace HTMLPurifier\HTMLModule;

use HTMLPurifier\HTMLModule;
use \HTMLPurifier\Config;

/**
 * Module adds the nofollow attribute transformation to a tags.  It
 * is enabled by HTML.Nofollow
 */
class Nofollow extends HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Nofollow';

    /**
     * @param \HTMLPurifier\Config $config
     */
    public function setup(\HTMLPurifier\Config $config): void
    {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new \HTMLPurifier\AttrTransform\Nofollow();
    }
}
