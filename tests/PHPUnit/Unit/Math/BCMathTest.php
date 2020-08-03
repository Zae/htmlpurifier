<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Math;

use HTMLPurifier\Math\MathFactory;

/**
 * Class BCMathTest
 *
 * @package HTMLPurifier\Tests\Unit\Math
 */
class BCMathTest extends MathTest
{
    protected function setUp(): void
    {
        if (!\extension_loaded('bcmath')) {
            static::markTestSkipped('BCMath not loaded');
        }

        parent::setUp();
        $this->obj = MathFactory::make(false);
    }

    /**
     * FIXME: Somehow the rounding of the bcmath adapter is different from native?
     * @return array
     */
    public function roundProvider(): array
    {
        return [
            ["0", "3.3333333333", 0],
            ["3", "3.3333333333", 1],
            ["3.3", "3.3333333333", 2],
            ["3.33", "3.3333333333", 3],
            ["3.333", "3.3333333333", 4],
        ];
    }
}
