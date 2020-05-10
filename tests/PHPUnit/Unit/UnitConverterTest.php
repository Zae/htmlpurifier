<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Length;
use HTMLPurifier\UnitConverter;

/**
 * Class UnitConverterTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class UnitConverterTest extends TestCase
{
    /**
     * @param      $input
     * @param      $expect
     * @param null $unit
     * @param bool $test_negative
     */
    private function assertConversion($input, $expect, $unit = null, $test_negative = true): void
    {
        $length = Length::make($input);

        if ($expect !== false) {
            $expectl = Length::make($expect);
        } else {
            $expectl = false;
        }

        $to_unit = $unit ?? $expectl->getUnit();

        $converter = new UnitConverter(4, 10);
        $result = $converter->convert($length, $to_unit);
        if (!$result || !$expectl) {
            static::assertEquals($expectl, $result);
        } else {
            static::assertEquals($expectl->toString(), $result->toString());
        }

        $converter = new UnitConverter(4, 10, true);
        $result = $converter->convert($length, $to_unit);
        if (!$result || !$expectl) {
            static::assertEquals($expectl, $result);
        } else {
            static::assertEquals($expectl->toString(), $result->toString(), 'BCMath substitute: %s');
        }

        if ($test_negative) {
            $this->assertConversion(
                "-$input",
                $expect === false ? false : "-$expect",
                $unit,
                false
            );
        }
    }

    /**
     * @test
     */
    public function testFail(): void
    {
        $this->assertConversion('1in', false, 'foo');
        $this->assertConversion('1foo', false, 'in');
    }

    /**
     * @test
     */
    public function testZero(): void
    {
        $this->assertConversion('0', '0', 'in', false);
        $this->assertConversion('-0', '0', 'in', false);
        $this->assertConversion('0in', '0', 'in', false);
        $this->assertConversion('-0in', '0', 'in', false);
        $this->assertConversion('0in', '0', 'pt', false);
        $this->assertConversion('-0in', '0', 'pt', false);
    }

    /**
     * @test
     */
    public function testEnglish(): void
    {
        $this->assertConversion('1in', '6pc');
        $this->assertConversion('6pc', '1in');

        $this->assertConversion('1in', '72pt');
        $this->assertConversion('72pt', '1in');

        $this->assertConversion('1pc', '12pt');
        $this->assertConversion('12pt', '1pc');

        $this->assertConversion('1pt', '0.01389in');
        $this->assertConversion('1.000pt', '0.01389in');
        $this->assertConversion('100000pt', '1389in');

        $this->assertConversion('1in', '96px');
        $this->assertConversion('96px', '1in');
    }

    /**
     * @test
     */
    public function testMetric(): void
    {
        $this->assertConversion('1cm', '10mm');
        $this->assertConversion('10mm', '1cm');
        $this->assertConversion('1mm', '0.1cm');
        $this->assertConversion('100mm', '10cm');
    }

    /**
     * @test
     */
    public function testEnglishMetric(): void
    {
        $this->assertConversion('2.835pt', '1mm');
        $this->assertConversion('1mm', '2.835pt');
        $this->assertConversion('0.3937in', '1cm');
    }

    /**
     * @test
     */
    public function testRoundingMinPrecision(): void
    {
        // One sig-fig, modified to be four, conversion rounds up
        $this->assertConversion('100pt', '1.389in');
        $this->assertConversion('1000pt', '13.89in');
        $this->assertConversion('10000pt', '138.9in');
        $this->assertConversion('100000pt', '1389in');
        $this->assertConversion('1000000pt', '13890in');
    }

    /**
     * @test
     */
    public function testRoundingUserPrecision(): void
    {
        // Five sig-figs, conversion rounds down
        $this->assertConversion('11112000pt', '154330in');
        $this->assertConversion('1111200pt', '15433in');
        $this->assertConversion('111120pt', '1543.3in');
        $this->assertConversion('11112pt', '154.33in');
        $this->assertConversion('1111.2pt', '15.433in');
        $this->assertConversion('111.12pt', '1.5433in');
        $this->assertConversion('11.112pt', '0.15433in');
    }

    /**
     * @test
     */
    public function testRoundingBigNumber(): void
    {
        $this->assertConversion('444400000000000000000000in', '42660000000000000000000000px');
    }

    /**
     * @param $n
     * @param $sigfigs
     */
    private function assertSigFig($n, $sigfigs): void
    {
        $converter = new UnitConverter();
        $result = $converter->getSigFigs($n);

        static::assertEquals($sigfigs, $result);
    }

    /**
     * @test
     */
    public function test_getSigFigs(): void
    {
        $this->assertSigFig('0', 0);
        $this->assertSigFig('1', 1);
        $this->assertSigFig('-1', 1);
        $this->assertSigFig('+1', 1);
        $this->assertSigFig('01', 1);
        $this->assertSigFig('001', 1);
        $this->assertSigFig('12', 2);
        $this->assertSigFig('012', 2);
        $this->assertSigFig('10', 1);
        $this->assertSigFig('10.', 2);
        $this->assertSigFig('100.', 3);
        $this->assertSigFig('103', 3);
        $this->assertSigFig('130', 2);
        $this->assertSigFig('.1', 1);
        $this->assertSigFig('0.1', 1);
        $this->assertSigFig('00.1', 1);
        $this->assertSigFig('0.01', 1);
        $this->assertSigFig('0.010', 2);
        $this->assertSigFig('0.012', 2);
    }
}
