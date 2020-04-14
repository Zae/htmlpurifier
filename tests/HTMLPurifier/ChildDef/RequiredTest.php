<?php

use HTMLPurifier\ChildDef\Required;

class HTMLPurifier_ChildDef_RequiredTest extends HTMLPurifier_ChildDefHarness
{

    public function testPrepareString()
    {
        $def = new Required('foobar | bang |gizmo');
        $this->assertIdentical($def->elements,
          array(
            'foobar' => true
           ,'bang'   => true
           ,'gizmo'  => true
          ));
    }

    public function testPrepareArray()
    {
        $def = new Required(array('href', 'src'));
        $this->assertIdentical($def->elements,
          array(
            'href' => true
           ,'src'  => true
          ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->obj = new Required('dt | dd');
    }

    public function testEmptyInput()
    {
        $this->assertResult('', false);
    }

    public function testRemoveIllegalTagsAndElements()
    {
        $this->assertResult(
          '<dt>Term</dt>Text in an illegal location'.
             '<dd>Definition</dd><b>Illegal tag</b>',
          '<dt>Term</dt><dd>Definition</dd>');
        $this->assertResult('How do you do!', false);
    }

    public function testIgnoreWhitespace()
    {
        // whitespace shouldn't trigger it
        $this->assertResult("\n<dd>Definition</dd>       ");
    }

    public function testPreserveWhitespaceAfterRemoval()
    {
        $this->assertResult(
          '<dd>Definition</dd>       <b></b>       ',
          '<dd>Definition</dd>              '
        );
    }

    public function testDeleteNodeIfOnlyWhitespace()
    {
        $this->assertResult("\t      ", false);
    }

    public function testPCDATAAllowed()
    {
        $this->obj = new Required('#PCDATA | b');
        $this->assertResult('Out <b>Bold text</b><img />', 'Out <b>Bold text</b>');
    }
    public function testPCDATAAllowedJump()
    {
        $this->obj = new Required('#PCDATA | b');
        $this->assertResult('A <i>foo</i>', 'A foo');
    }
}

// vim: et sw=4 sts=4
