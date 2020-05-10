<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef;

use HTMLPurifier\AttrDef\Integer;

/**
 * Class IntegerTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef
 */
class IntegerTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->def = new Integer();

        $this->assertDef('0');
        $this->assertDef('1');
        $this->assertDef('-1');
        $this->assertDef('-10');
        $this->assertDef('14');
        $this->assertDef('+24', '24');
        $this->assertDef(' 14 ', '14');
        $this->assertDef('-0', '0');

        $this->assertDef('-1.4', false);
        $this->assertDef('3.4', false);
        $this->assertDef('asdf', false); // must not return zero
        $this->assertDef('2in', false); // must not return zero
    }

    /**
     * @param $negative
     * @param $zero
     * @param $positive
     */
    private function assertRange($negative, $zero, $positive): void
    {
        $this->assertDef('-100', $negative);
        $this->assertDef('-1', $negative);
        $this->assertDef('0', $zero);
        $this->assertDef('1', $positive);
        $this->assertDef('42', $positive);
    }

    /**
     * @test
     */
    public function testRange(): void
    {
        $this->def = new Integer(false);
        $this->assertRange(false, true, true); // non-negative

        $this->def = new Integer(false, false);
        $this->assertRange(false, false, true); // positive


        // fringe cases

        $this->def = new Integer(false, false, false);
        $this->assertRange(false, false, false); // allow none

        $this->def = new Integer(true, false, false);
        $this->assertRange(true, false, false); // negative

        $this->def = new Integer(false, true, false);
        $this->assertRange(false, true, false); // zero

        $this->def = new Integer(true, true, false);
        $this->assertRange(true, true, false); // non-positive
    }
}
