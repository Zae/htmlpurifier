<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\BoolToCSS;

/**
 * Class BoolToCssTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class BoolToCssTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new BoolToCSS('foo', 'bar:3in;');
    }

    /**
     * @test
     */
    public function testEmptyInput(): void
    {
        $this->assertResult([]);
    }

    /**
     * @test
     */
    public function testBasicTransform(): void
    {
        $this->assertResult(
            ['foo' => 'foo'],
            ['style' => 'bar:3in;']
        );
    }

    /**
     * @test
     */
    public function testIgnoreValueOfBooleanAttribute(): void
    {
        $this->assertResult(
            ['foo' => 'no'],
            ['style' => 'bar:3in;']
        );
    }

    /**
     * @test
     */
    public function testPrependCSS(): void
    {
        $this->assertResult(
            ['foo' => 'foo', 'style' => 'background-color:#F00;'],
            ['style' => 'bar:3in;background-color:#F00;']
        );
    }
}
