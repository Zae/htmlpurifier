<?php

use HTMLPurifier\AttrTransform\BdoDir;

class HTMLPurifier_AttrTransform_BdoDirTest extends HTMLPurifier_AttrTransformHarness
{

    public function setUp()
    {
        parent::setUp();
        $this->obj = new BdoDir();
    }

    public function testAddDefaultDir()
    {
        $this->assertResult( array(), array('dir' => 'ltr') );
    }

    public function testPreserveExistingDir()
    {
        $this->assertResult( array('dir' => 'rtl') );
    }

    public function testAlternateDefault()
    {
        $this->config->set('Attr.DefaultTextDir', 'rtl');
        $this->assertResult(
            array(),
            array('dir' => 'rtl')
        );

    }

}

// vim: et sw=4 sts=4
