<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\Length;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class LengthTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class LengthTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new Length();

        $this->assertDef('0');
        $this->assertDef('0px');
        $this->assertDef('4.5px');
        $this->assertDef('-4.5px');
        $this->assertDef('3ex');
        $this->assertDef('3em');
        $this->assertDef('3in');
        $this->assertDef('3cm');
        $this->assertDef('3mm');
        $this->assertDef('3pt');
        $this->assertDef('3pc');

        $this->assertDef('3PX', '3px');

        $this->assertDef('3', false);
        $this->assertDef('3miles', false);
    }

    /**
     * @test
     */
    public function testNonNegative(): void
    {
        $this->def = new Length('0');

        $this->assertDef('3cm');
        $this->assertDef('-3mm', false);
    }

    /**
     * @test
     */
    public function testBounding(): void
    {
        $this->def = new Length('-1in', '1in');
        $this->assertDef('1cm');
        $this->assertDef('-1cm');
        $this->assertDef('0');
        $this->assertDef('1em', false);
    }
}
