<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\URI;

use HTMLPurifier\AttrDef\URI\IPv4;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class IPv4Test
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\URI
 */
class IPv4Test extends TestCase
{
    /**
     * @test
     */
    public function test(): void
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
