<?php

declare(strict_types=1);

/**
 * Module adds the nofollow attribute transformation to a tags.  It
 * is enabled by HTML.Nofollow
 */
class HTMLPurifier_HTMLModule_Nofollow extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Nofollow';

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup(HTMLPurifier_Config $config): void
    {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new HTMLPurifier_AttrTransform_Nofollow();
    }
}
