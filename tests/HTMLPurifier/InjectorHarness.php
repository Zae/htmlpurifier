<?php

use HTMLPurifier\Strategy\MakeWellFormed;

class HTMLPurifier_InjectorHarness extends HTMLPurifier_StrategyHarness
{

    public function setUp()
    {
        parent::setUp();
        $this->obj = new MakeWellFormed();
    }

}

// vim: et sw=4 sts=4
