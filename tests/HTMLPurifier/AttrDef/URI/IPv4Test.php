<?php

// IPv4 test case is spliced from Feyd's IPv6 implementation
// we ought to disallow non-routable addresses

use HTMLPurifier\AttrDef\URI\IPv4;

class HTMLPurifier_AttrDef_URI_IPv4Test extends HTMLPurifier_AttrDefHarness
{

    public function test()
    {
        $this->def = new IPv4();

        $this->assertDef('127.0.0.1'); // standard IPv4, loopback, non-routable
        $this->assertDef('0.0.0.0'); // standard IPv4, unspecified, non-routable
        $this->assertDef('255.255.255.255'); // standard IPv4

        $this->assertDef('300.0.0.0', false); // standard IPv4, out of range
        $this->assertDef('124.15.6.89/60', false); // standard IPv4, prefix not allowed

        $this->assertDef('', false); // nothing

    }
}

// vim: et sw=4 sts=4
