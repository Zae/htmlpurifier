<?php

use HTMLPurifier\Exception;
use HTMLPurifier\PropertyList;

class HTMLPurifier_PropertyListTest extends UnitTestCase
{

    public function testBasic()
    {
        $plist = new PropertyList();
        $plist->set('key', 'value');
        $this->assertIdentical($plist->get('key'), 'value');
    }

    public function testNotFound()
    {
        $this->expectException(new Exception("Key 'key' not found"));
        $plist = new PropertyList();
        $plist->get('key');
    }

    public function testRecursion()
    {
        $parent_plist = new PropertyList();
        $parent_plist->set('key', 'value');
        $plist = new PropertyList();
        $plist->setParent($parent_plist);
        $this->assertIdentical($plist->get('key'), 'value');
    }

    public function testOverride()
    {
        $parent_plist = new PropertyList();
        $parent_plist->set('key', 'value');
        $plist = new PropertyList();
        $plist->setParent($parent_plist);
        $plist->set('key',  'value2');
        $this->assertIdentical($plist->get('key'), 'value2');
    }

    public function testRecursionNotFound()
    {
        $this->expectException(new Exception("Key 'key' not found"));
        $parent_plist = new PropertyList();
        $plist = new PropertyList();
        $plist->setParent($parent_plist);
        $this->assertIdentical($plist->get('key'), 'value');
    }

    public function testHas()
    {
        $plist = new PropertyList();
        $this->assertIdentical($plist->has('key'), false);
        $plist->set('key', 'value');
        $this->assertIdentical($plist->has('key'), true);
    }

    public function testReset()
    {
        $plist = new PropertyList();
        $plist->set('key1', 'value');
        $plist->set('key2', 'value');
        $plist->set('key3', 'value');
        $this->assertIdentical($plist->has('key1'), true);
        $this->assertIdentical($plist->has('key2'), true);
        $this->assertIdentical($plist->has('key3'), true);
        $plist->reset('key2');
        $this->assertIdentical($plist->has('key1'), true);
        $this->assertIdentical($plist->has('key2'), false);
        $this->assertIdentical($plist->has('key3'), true);
        $plist->reset();
        $this->assertIdentical($plist->has('key1'), false);
        $this->assertIdentical($plist->has('key2'), false);
        $this->assertIdentical($plist->has('key3'), false);
    }

    public function testSquash()
    {
        $parent = new PropertyList();
        $parent->set('key1', 'hidden');
        $parent->set('key2', 2);
        $plist = new PropertyList($parent);
        $plist->set('key1', 1);
        $plist->set('key3', 3);
        $this->assertIdentical(
            $plist->squash(),
            array('key1' => 1, 'key2' => 2, 'key3' => 3)
        );
        // updates don't show up...
        $plist->set('key2', 22);
        $this->assertIdentical(
            $plist->squash(),
            array('key1' => 1, 'key2' => 2, 'key3' => 3)
        );
        // until you force
        $this->assertIdentical(
            $plist->squash(true),
            array('key1' => 1, 'key2' => 22, 'key3' => 3)
        );
    }
}

// vim: et sw=4 sts=4
