<?php

use HTMLPurifier\StringHash;

class HTMLPurifier_StringHashTest extends UnitTestCase
{

    public function testUsed()
    {
        $hash = new StringHash(array(
            'key' => 'value',
            'key2' => 'value2'
        ));
        $this->assertIdentical($hash->getAccessed(), array());
        $t = $hash->offsetGet('key');
        $this->assertIdentical($hash->getAccessed(), array('key' => true));
        $hash->resetAccessed();
        $this->assertIdentical($hash->getAccessed(), array());
    }

}

// vim: et sw=4 sts=4
