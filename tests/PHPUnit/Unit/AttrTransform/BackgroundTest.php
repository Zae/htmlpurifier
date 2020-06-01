<?php

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\Background;

class BackgroundTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->obj = new Background();
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
            ['background' => 'logo.png'],
            ['style' => 'background-image:url(logo.png);']
        );
    }

    /**
     * @test
     */
    public function testPrependNewCSS(): void
    {
        $this->assertResult(
            ['background' => 'logo.png', 'style' => 'font-weight:bold'],
            ['style' => 'background-image:url(logo.png);font-weight:bold']
        );
    }

    /**
     * @test
     */
    public function testLenientTreatmentOfInvalidInput(): void
    {
        // notice that we rely on the CSS validator later to fix this invalid
        // stuff
        $this->assertResult(
            ['background' => 'logo.png);foo:('],
            ['style' => 'background-image:url(logo.png);foo:();']
        );
    }
}
