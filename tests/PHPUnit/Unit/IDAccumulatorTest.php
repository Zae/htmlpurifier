<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\IDAccumulator;

/**
 * Class IDAccumulatorTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class IDAccumulatorTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        // initialize the accumulator
        $accumulator = new IDAccumulator();

        static::assertTrue($accumulator->add('id1'));
        static::assertTrue($accumulator->add('id2'));
        static::assertFalse($accumulator->add('id1')); // repeated id

        // you can also access the properties (they're public)
        static::assertTrue(isset($accumulator->ids['id2']));
    }

    /**
     * @test
     */
    public function testLoad(): void
    {
        $accumulator = new IDAccumulator();

        $accumulator->load(['id1', 'id2', 'id3']);

        static::assertFalse($accumulator->add('id1')); // repeated id
        static::assertTrue($accumulator->add('id4'));
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function testBuild(): void
    {
        $this->config->set('Attr.IDBlacklist', ['foo']);
        $accumulator = IDAccumulator::build($this->config, $this->context);
        static::assertTrue(isset($accumulator->ids['foo']));
    }
}
