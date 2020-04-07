<?php

use HTMLPurifier\AttrDef\Text;
use HTMLPurifier\AttrDef\Enum;

class HTMLPurifier_AttrTypesTest extends HTMLPurifier_Harness
{

    public function test_get()
    {
        $types = new HTMLPurifier_AttrTypes();

        $this->assertIdentical(
            $types->get('CDATA'),
            new Text()
        );

        $this->expectError('Cannot retrieve undefined attribute type foobar');
        $types->get('foobar');

        $this->assertIdentical(
            $types->get('Enum#foo,bar'),
            new Enum(array('foo', 'bar'))
        );

    }

}

// vim: et sw=4 sts=4
