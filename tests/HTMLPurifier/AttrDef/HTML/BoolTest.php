<?php

use HTMLPurifier\AttrDef\HTML\Boolean;

class HTMLPurifier_AttrDef_HTML_BoolTest extends HTMLPurifier_AttrDefHarness
{

    public function test()
    {
        $this->def = new Boolean('foo');
        $this->assertDef('foo');
        $this->assertDef('', 'foo');
        $this->assertDef('bar', 'foo');
    }

    public function test_make()
    {
        $factory = new Boolean();
        $def = $factory->make('foo');
        $def2 = new Boolean('foo');
        $this->assertIdentical($def, $def2);
    }

}

// vim: et sw=4 sts=4
