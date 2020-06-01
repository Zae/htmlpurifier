<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\EnumToCSS;

/**
 * Class EnumToCssTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class EnumToCssTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new EnumToCSS('align', [
            'left' => 'text-align:left;',
            'right' => 'text-align:right;'
        ]);
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
    public function testPreserveArraysWithoutInterestingAttributes(): void
    {
        $this->assertResult(['style' => 'font-weight:bold;']);
    }

    /**
     * @test
     */
    public function testConvertAlignLeft(): void
    {
        $this->assertResult(
            ['align' => 'left'],
            ['style' => 'text-align:left;']
        );
    }

    /**
     * @test
     */
    public function testConvertAlignRight(): void
    {
        $this->assertResult(
            ['align' => 'right'],
            ['style' => 'text-align:right;']
        );
    }

    /**
     * @test
     */
    public function testRemoveInvalidAlign(): void
    {
        $this->assertResult(
            ['align' => 'invalid'],
            []
        );
    }

    /**
     * @test
     */
    public function testPrependNewCSS(): void
    {
        $this->assertResult(
            ['align' => 'left', 'style' => 'font-weight:bold;'],
            ['style' => 'text-align:left;font-weight:bold;']
        );
    }

    /**
     * @test
     */
    public function testCaseInsensitive(): void
    {
        $this->obj = new EnumToCSS('align', [
            'right' => 'text-align:right;'
        ]);

        $this->assertResult(
            ['align' => 'RIGHT'],
            ['style' => 'text-align:right;']
        );
    }

    /**
     * @test
     */
    public function testCaseSensitive(): void
    {
        $this->obj = new EnumToCSS('align', [
            'right' => 'text-align:right;'
        ], true);

        $this->assertResult(
            ['align' => 'RIGHT'],
            []
        );
    }
}
