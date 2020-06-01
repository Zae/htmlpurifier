<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\Length;

/**
 * Class LengthTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class LengthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Length('width');
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
    public function testTransformPixel(): void
    {
        $this->assertResult(
            ['width' => '10'],
            ['style' => 'width:10px;']
        );
    }

    /**
     * @test
     */
    public function testTransformPercentage(): void
    {
        $this->assertResult(
            ['width' => '10%'],
            ['style' => 'width:10%;']
        );
    }

    /**
     * @test
     */
    public function testPrependNewCSS(): void
    {
        $this->assertResult(
            ['width' => '10%', 'style' => 'font-weight:bold'],
            ['style' => 'width:10%;font-weight:bold']
        );
    }

    /**
     * @test
     */
    public function testLenientTreatmentOfInvalidInput(): void
    {
        $this->assertResult(
            ['width' => 'asdf'],
            ['style' => 'width:asdf;']
        );
    }
}
