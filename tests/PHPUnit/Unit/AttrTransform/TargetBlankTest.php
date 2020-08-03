<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrTransform;

use HTMLPurifier\AttrTransform\TargetBlank;

/**
 * Class TargetBlankTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrTransform
 */
class TargetBlankTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new TargetBlank();
    }

    /**
     * @test
     */
    public function test(): void
    {
        $attr = ['xxx' => true];

        static::assertEquals(
            $attr,
            $this->obj->transform($attr, $this->config, $this->context)
        );
    }
}
