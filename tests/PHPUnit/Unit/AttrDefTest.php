<?php
declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\AttrDef;
use Mockery;

/**
 * Class AttrDefTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class AttrDefTest extends TestCase
{
    /**
     * @test
     */
    public function testParseCDATA(): void
    {
        $def = Mockery::mock(AttrDef::class)->makePartial();

        static::assertEquals('', $def->parseCDATA(''));
        static::assertEquals('', $def->parseCDATA("\t\n\r \t\t"));
        static::assertEquals('foo', $def->parseCDATA("\t\n\r foo\t\t"));
        static::assertEquals('translate to space', $def->parseCDATA("translate\nto\tspace"));
    }

    /**
     * @test
     */
    public function testMake(): void
    {
        $def = Mockery::mock(AttrDef::class)->makePartial();

        $def2 = $def->make('');
        static::assertEquals($def, $def2);
    }
}
