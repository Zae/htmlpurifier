<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\Percentage;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class PercentageTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class PercentageTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new Percentage();

        $this->assertDef('10%');
        $this->assertDef('1.607%');
        $this->assertDef('-567%');

        $this->assertDef(' 100% ', '100%');

        $this->assertDef('5', false);
        $this->assertDef('asdf', false);
        $this->assertDef('%', false);
    }
}
