<?php

use HTMLPurifier\AttrDef\Enum;

class HTMLPurifier_AttrDef_EnumTest extends HTMLPurifier_AttrDefHarness
{

    public function testCaseInsensitive()
    {
        $this->def = new Enum(array('one', 'two'));
        $this->assertDef('one');
        $this->assertDef('ONE', 'one');
    }

    public function testCaseSensitive()
    {
        $this->def = new Enum(array('one', 'two'), true);
        $this->assertDef('one');
        $this->assertDef('ONE', false);
    }

    public function testFixing()
    {
        $this->def = new Enum(array('one'));
        $this->assertDef(' one ', 'one');
    }

    public function test_make()
    {
        $factory = new Enum();

        $def = $factory->make('foo,bar');
        $def2 = new Enum(array('foo', 'bar'));
        $this->assertIdentical($def, $def2);

        $def = $factory->make('s:foo,BAR');
        $def2 = new Enum(array('foo', 'BAR'), true);
        $this->assertIdentical($def, $def2);
    }

}

// vim: et sw=4 sts=4
