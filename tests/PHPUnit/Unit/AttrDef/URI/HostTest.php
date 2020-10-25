<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\URI;

use HTMLPurifier\AttrDef\URI\Host;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class HostTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\URI
 */
class HostTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->def = new Host();
    }

    /**
     * @test
     */
    public function test(): void
    {
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

    /**
     * @test
     * @group idna
     */
    public function testIDNA(): void
    {
        if (!\function_exists("idn_to_ascii")) {
            static::markTestSkipped('idn_to_ascii does not exist');
        }

        $this->assertDef("\xE4\xB8\xAD\xE6\x96\x87.com.cn", "xn--fiq228c.com.cn");
        $this->assertDef("faß.de", "xn--fa-hia.de");
        $this->assertDef("\xe2\x80\x85.com", null); // rejected
    }

    /**
     * @test
     */
    public function testAllowUnderscore(): void
    {
        $this->config->set('Core.AllowHostnameUnderscore', true);

        $this->assertDef("foo_bar.example.com");
        $this->assertDef("foo_.example.com", null);
    }
}
