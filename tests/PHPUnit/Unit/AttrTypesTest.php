<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\AttrDef\Enum;
use HTMLPurifier\AttrDef\Text;
use HTMLPurifier\AttrTypes;
use HTMLPurifier\Exception;

/**
 * Class AttrTypesTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class AttrTypesTest extends TestCase
{
    /**
     * @test
     */
    public function test_get(): void
    {
        $types = new AttrTypes();

        static::assertEquals(
            $types->get('CDATA'),
            new Text()
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot retrieve undefined attribute type foobar');

        $types->get('foobar');

        static::assertEquals(
            $types->get('Enum#foo,bar'),
            new Enum(['foo', 'bar'])
        );
    }
}
