<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\HTML;

use HTMLPurifier\AttrDef\HTML\Length;

/**
 * Class LengthTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\HTML
 */
class LengthTest extends PixelsTest
{
    protected function setUp(): void
    {
        $this->def = new Length();
    }

    /**
     * @test
     */
    public function test(): void
    {
        // pixel check
        parent::test();

        // percent check
        $this->assertDef('25%');

        // Firefox maintains percent, so will we
        $this->assertDef('0%');

        // 0% <= percent <= 100%
        $this->assertDef('-15%', '0%');
        $this->assertDef('120%', '100%');

        // fractional percents, apparently, aren't allowed
        $this->assertDef('56.5%', '56%');
    }
}
