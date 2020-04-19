<?php

use HTMLPurifier\Strategy\Core;

class HTMLPurifier_HTMLModuleHarness extends HTMLPurifier_StrategyHarness
{
    public function setup()
    {
        parent::setup();
        $this->obj = new Core();
    }
}

// vim: et sw=4 sts=4
