<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\Border;

/**
 * Class BorderTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class BorderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Border();
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
            ['border' => '1'],
            ['style' => 'border:1px solid;']
        );
    }

    /**
     * @test
     */
    public function testLenientTreatmentOfInvalidInput(): void
    {
        $this->assertResult(
            ['border' => '10%'],
            ['style' => 'border:10%px solid;']
        );
    }

    /**
     * @test
     */
    public function testPrependNewCSS(): void
    {
        $this->assertResult(
            ['border' => '23', 'style' => 'font-weight:bold;'],
            ['style' => 'border:23px solid;font-weight:bold;']
        );
    }
}
