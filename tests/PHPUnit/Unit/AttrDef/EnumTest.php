<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef;

use HTMLPurifier\AttrDef\Enum;

/**
 * Class EnumTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef
 */
class EnumTest extends TestCase
{
    /**
     * @test
     */
    public function testCaseInsensitive(): void
    {
        $this->def = new Enum(['one', 'two']);
        $this->assertDef('one');
        $this->assertDef('ONE', 'one');
    }

    /**
     * @test
     */
    public function testCaseSensitive(): void
    {
        $this->def = new Enum(['one', 'two'], true);
        $this->assertDef('one');
        $this->assertDef('ONE', false);
    }

    /**
     * @test
     */
    public function testFixing()
    {
        $this->def = new Enum(['one']);
        $this->assertDef(' one ', 'one');
    }

    /**
     * @test
     */
    public function test_make(): void
    {
        $factory = new Enum();

        $def = $factory->make('foo,bar');
        $def2 = new Enum(['foo', 'bar']);
        static::assertEquals($def, $def2);

        $def = $factory->make('s:foo,BAR');
        $def2 = new Enum(['foo', 'BAR'], true);
        static::assertEquals($def, $def2);
    }
}
