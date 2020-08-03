<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Math;

use HTMLPurifier\Math\MathFactory;

/**
 * Class NativeMathTest
 *
 * @package HTMLPurifier\Tests\Unit\Math
 */
class NativeMathTest extends MathTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = MathFactory::make(true);
    }
}
