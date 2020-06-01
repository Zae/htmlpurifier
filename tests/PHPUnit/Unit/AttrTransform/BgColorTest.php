<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\BgColor;

/**
 * Class BgColorTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class BgColorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new BgColor();
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
            ['bgcolor' => '#000000'],
            ['style' => 'background-color:#000000;']
        );
    }

    /**
     * @test
     */
    public function testPrependNewCSS(): void
    {
        $this->assertResult(
            ['bgcolor' => '#000000', 'style' => 'font-weight:bold'],
            ['style' => 'background-color:#000000;font-weight:bold']
        );
    }

    /**
     * @test
     */
    public function testLenientTreatmentOfInvalidInput(): void
    {
        // this may change when we natively support the datatype and
        // validate its contents before forwarding it on
        $this->assertResult(
            ['bgcolor' => '#F00'],
            ['style' => 'background-color:#F00;']
        );
    }
}
