<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Length;

/**
 * Class LengthTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class LengthTest extends TestCase
{
    /**
     * @test
     */
    public function testConstruct(): void
    {
        $l = new Length('23', 'in');

        static::assertEquals('23', $l->getN());
        static::assertEquals('in', $l->getUnit());
    }

    /**
     * @test
     */
    public function testMake(): void
    {
        $l = Length::make('+23.4in');

        static::assertEquals('+23.4', $l->getN());
        static::assertEquals('in', $l->getUnit());
    }

    /**
     * @test
     */
    public function testToString(): void
    {
        $l = new Length('23', 'in');

        static::assertEquals('23in', $l->toString());
    }

    /**
     * @param string      $string
     * @param bool|string $expect
     */
    private function assertValidate(string $string, $expect = true): void
    {
        if ($expect === true) {
            $expect = $string;
        }

        $l = Length::make($string);
        $result = $l->isValid();

        if ($result === false) {
            static::assertEquals($expect, false);
        } else {
            static::assertEquals($expect, $l->toString());
        }
    }

    /**
     * @test
     */
    public function testValidate(): void
    {
        $this->assertValidate('0');
        $this->assertValidate('+0', '0');
        $this->assertValidate('-0', '0');
        $this->assertValidate('0px');
        $this->assertValidate('4.5px');
        $this->assertValidate('-4.5px');
        $this->assertValidate('3ex');
        $this->assertValidate('3em');
        $this->assertValidate('3in');
        $this->assertValidate('3cm');
        $this->assertValidate('3mm');
        $this->assertValidate('3pt');
        $this->assertValidate('3pc');
        $this->assertValidate('3PX', '3px');
        $this->assertValidate('3', false);
        $this->assertValidate('3miles', false);
    }

    /**
     * @param string $s1 First string to compare
     * @param string $s2 Second string to compare
     * @param int $expect 0 for $s1 == $s2, 1 for $s1 > $s2 and -1 for $s1 < $s2
     */
    private function assertComparison($s1, $s2, $expect = 0): void
    {
        $l1 = Length::make($s1);
        $l2 = Length::make($s2);
        $r1 = $l1->compareTo($l2);
        $r2 = $l2->compareTo($l1);

        static::assertEquals($r1 === 0 ? 0 : ($r1 > 0 ? 1 : -1), $expect);
        static::assertEquals($r2 === 0 ? 0 : ($r2 > 0 ? 1 : -1), - $expect);
    }

    /**
     * @test
     */
    public function testCompareTo(): void
    {
        $this->assertComparison('12in', '12in');
        $this->assertComparison('12in', '12mm', 1);
        $this->assertComparison('1px', '1mm', -1);
        $this->assertComparison(str_repeat('2', 38) . 'in', '100px', 1);
    }
}
