<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Math;

use HTMLPurifier\Math\MathFactory;
use HTMLPurifier\Tests\Unit\TestCase;

/**
 * Class BCMathTest
 *
 * @package HTMLPurifier\Tests\Unit\Math
 */
abstract class MathTest extends TestCase
{
    /**
     * @var \HTMLPurifier\Math\MathInterface
     */
    protected $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = MathFactory::make(false);
    }

    /**
     * @test
     *
     * @param $expected
     * @param $one
     * @param $two
     * @param $scale
     * @param $func
     *
     * @dataProvider provider
     */
    public function test($expected, $one, $two, $scale, $func): void
    {
        static::assertEquals($expected, $this->obj->{$func}($one, $two, $scale));
    }

    /**
     * @test
     *
     * @param $expected
     * @param $n
     * @param $sigfigs
     *
     * @dataProvider roundProvider
     */
    public function testRound($expected, $n, $sigfigs): void
    {
        static::assertEquals($expected, $this->obj->round($n, $sigfigs));
    }

    /**
     * @return array
     */
    public function provider(): array
    {
        return [
            ["2", "1", "1", 0, "add"],
            ["2.0", "1", "1", 1, "add"],
            ["2.5", "1", "1.5", 1, "add"],
            ["2.50", "1", "1.5", 2, "add"],
            ["4", "2", "2", 0, "multiply"],
            ["2.5", "1.25", "2", 1, "multiply"],
            ["2.50", "1.25", "2", 2, "multiply"],
            ["3", "10", "3", 0, "divide"],
            ["3.3", "10", "3", 1, "divide"],
            ["3.33", "10", "3", 2, "divide"],
            ["0", "0", "10", 0, "divide"],
        ];
    }

    /**
     * @return array
     */
    public function roundProvider(): array
    {
        return [
            ["0", "3.3333333333", 0],
            ["3.0", "3.3333333333", 1],
            ["3.30", "3.3333333333", 2],
            ["3.330", "3.3333333333", 3],
            ["3.3330", "3.3333333333", 4],
        ];
    }
}
