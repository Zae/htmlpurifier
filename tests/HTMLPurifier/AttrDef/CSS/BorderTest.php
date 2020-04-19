<?php

use HTMLPurifier\AttrDef\CSS\Border;

class HTMLPurifier_AttrDef_CSS_BorderTest extends HTMLPurifier_AttrDefHarness
{

    public function test()
    {
        $config = \HTMLPurifier\Config::createDefault();
        $this->def = new Border($config);

        $this->assertDef('thick solid red', 'thick solid #FF0000');
        $this->assertDef('thick solid');
        $this->assertDef('solid red', 'solid #FF0000');
        $this->assertDef('1px solid #000');
        $this->assertDef('1px solid rgb(0, 0, 0)', '1px solid rgb(0,0,0)');

    }

}

// vim: et sw=4 sts=4
