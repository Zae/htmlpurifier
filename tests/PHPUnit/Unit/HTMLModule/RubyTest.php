<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class RubyTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class RubyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('HTML.Doctype', 'XHTML 1.1');
    }

    /**
     * @test
     */
    public function testBasicUse(): void
    {
        $this->assertResult(
            '<ruby><rb>WWW</rb><rt>World Wide Web</rt></ruby>'
        );
    }

    /**
     * @test
     */
    public function testRPUse(): void
    {
        $this->assertResult(
            '<ruby><rb>WWW</rb><rp>(</rp><rt>World Wide Web</rt><rp>)</rp></ruby>'
        );
    }

    /**
     * @test
     */
    public function testComplexUse(): void
    {
        $this->assertResult(
            '<ruby>
  <rbc>
    <rb>10</rb>
    <rb>31</rb>
    <rb>2002</rb>
  </rbc>
  <rtc>
    <rt>Month</rt>
    <rt>Day</rt>
    <rt>Year</rt>
  </rtc>
  <rtc>
    <rt rbspan="3">Expiration Date</rt>
  </rtc>
</ruby>'
        );
    }

    /**
     * @test
     */
    public function testBackwardsCompat(): void
    {
        static::markTestSkipped('not implemented');

        $this->assertResult(
            '<ruby>A<rp>(</rp><rt>aaa</rt><rp>)</rp></ruby>',
            '<ruby><rb>A</rb><rp>(</rp><rt>aaa</rt><rp>)</rp></ruby>'
        );
    }
}
