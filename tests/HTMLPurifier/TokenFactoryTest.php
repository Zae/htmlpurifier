<?php

use HTMLPurifier\TokenFactory;
use HTMLPurifier\Token\Start;

class HTMLPurifier_TokenFactoryTest extends HTMLPurifier_Harness
{
    public function test()
    {
        $factory = new TokenFactory();

        $regular = new Start('a', array('href' => 'about:blank'));
        $generated = $factory->createStart('a', array('href' => 'about:blank'));

        $this->assertIdentical($regular, $generated);

    }
}

// vim: et sw=4 sts=4
