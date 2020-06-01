<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\CSS;

use HTMLPurifier\AttrDef\CSS\Number;
use HTMLPurifier\Tests\Unit\AttrDef\TestCase;

/**
 * Class NumberTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\CSS
 */
class NumberTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new Number();

        $this->assertDef('0');
        $this->assertDef('0.0', '0');
        $this->assertDef('1.0', '1');
        $this->assertDef('34');
        $this->assertDef('4.5');
        $this->assertDef('0.5');
        $this->assertDef('0.5', '0.5');
        $this->assertDef('-56.9');

        $this->assertDef('0.', '0');
        $this->assertDef('.0', '0');
        $this->assertDef('0.0', '0');

        $this->assertDef('1.', '1');
        $this->assertDef('.1', '0.1');

        $this->assertDef('1.0', '1');
        $this->assertDef('0.1', '0.1');

        $this->assertDef('000', '0');
        $this->assertDef(' 9', '9');
        $this->assertDef('+5.0000', '5');
        $this->assertDef('02.20', '2.2');
        $this->assertDef('2.', '2');

        $this->assertDef('.', false);
        $this->assertDef('asdf', false);
        $this->assertDef('0.5.6', false);
    }

    /**
     * @test
     */
    public function testNonNegative(): void
    {
        $this->def = new Number(true);
        $this->assertDef('23');
        $this->assertDef('-12', false);
    }
}
