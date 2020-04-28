<?php

// takes a URI formatted host and validates it

use HTMLPurifier\AttrDef\URI\Host;

class HTMLPurifier_AttrDef_URI_HostTest extends HTMLPurifier_AttrDefHarness
{

    public function test()
    {
        $this->def = new Host();

        $this->assertDef('[2001:DB8:0:0:8:800:200C:417A]'); // IPv6
        $this->assertDef('124.15.6.89'); // IPv4
        $this->assertDef('www.google.com'); // reg-name

        // more domain name tests
        $this->assertDef('test.');
        $this->assertDef('sub.test.');
        $this->assertDef('.test', null);
        $this->assertDef('ff');
        $this->assertDef('1f'); // per RFC 1123
        // See also http://serverfault.com/questions/638260/is-it-valid-for-a-hostname-to-start-with-a-digit
        $this->assertDef('-f', null);
        $this->assertDef('f1');
        $this->assertDef('f-', null);
        $this->assertDef('sub.ff');
        $this->assertDef('sub.1f'); // per RFC 1123
        $this->assertDef('sub.-f', null);
        $this->assertDef('sub.f1');
        $this->assertDef('sub.f-', null);
        $this->assertDef('ff.top');
        $this->assertDef('1f.top');
        $this->assertDef('-f.top', null);
        $this->assertDef('ff.top');
        $this->assertDef('f1.top');
        $this->assertDef('f1_f2.ex.top', null);
        $this->assertDef('f-.top', null);
        $this->assertDef('1a');

        $this->assertDef("\xE4\xB8\xAD\xE6\x96\x87.com.cn", 'xn--fiq228c.com.cn', true);

    }

    public function testIDNA()
    {
        if (!$GLOBALS['HTMLPurifierTest']['Net_IDNA2'] && !function_exists("idn_to_ascii")) {
            return false;
        }
        $this->config->set('Core.EnableIDNA', true);
        $this->assertDef("\xE4\xB8\xAD\xE6\x96\x87.com.cn", "xn--fiq228c.com.cn");
        $this->assertDef("faß.de", "xn--fa-hia.de");
        $this->assertDef("\xe2\x80\x85.com", null); // rejected
    }

    function testAllowUnderscore() {
        $this->config->set('Core.AllowHostnameUnderscore', true);
        $this->assertDef("foo_bar.example.com");
        $this->assertDef("foo_.example.com", null);
    }

}

// vim: et sw=4 sts=4
