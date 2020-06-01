<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\ImgSpace;

/**
 * Class ImgSpaceTEst
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class ImgSpaceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new ImgSpace('vspace');
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
    public function testVerticalBasicUsage(): void
    {
        $this->assertResult(
            ['vspace' => '1'],
            ['style' => 'margin-top:1px;margin-bottom:1px;']
        );
    }

    /**
     * @test
     */
    public function testLenientHandlingOfInvalidInput(): void
    {
        $this->assertResult(
            ['vspace' => '10%'],
            ['style' => 'margin-top:10%px;margin-bottom:10%px;']
        );
    }

    /**
     * @test
     */
    public function testPrependNewCSS(): void
    {
        $this->assertResult(
            ['vspace' => '23', 'style' => 'font-weight:bold;'],
            ['style' => 'margin-top:23px;margin-bottom:23px;font-weight:bold;']
        );
    }

    /**
     * @test
     */
    public function testHorizontalBasicUsage(): void
    {
        $this->obj = new ImgSpace('hspace');
        $this->assertResult(
            ['hspace' => '1'],
            ['style' => 'margin-left:1px;margin-right:1px;']
        );
    }

    /**
     * @test
     */
    public function testInvalidConstructionParameter(): void
    {
        $this->expectError();
        $this->expectErrorMessage('ispace is not valid space attribute');
        $this->obj = new ImgSpace('ispace');
        $this->assertResult(
            ['ispace' => '1'],
            []
        );
    }
}
