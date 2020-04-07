<?php

use HTMLPurifier\AttrDef\Text;

class HTMLPurifier_AttrDef_TextTest extends HTMLPurifier_AttrDefHarness
{

    public function test()
    {
        $this->def = new Text();

        $this->assertDef('This is spiffy text!');
        $this->assertDef(" Casual\tCDATA parse\ncheck. ", 'Casual CDATA parse check.');

    }

}

// vim: et sw=4 sts=4
