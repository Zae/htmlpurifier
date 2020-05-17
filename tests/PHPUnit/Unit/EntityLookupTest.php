<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\EntityLookup;

/**
 * Class EntityLookupTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class EntityLookupTest extends TestCase
{
    public function test()
    {
        $lookup = EntityLookup::instance();

        // latin char
        static::assertEquals('â', $lookup->table['acirc']);

        // special char
        static::assertEquals('"', $lookup->table['quot']);
        static::assertEquals('“', $lookup->table['ldquo']);
        static::assertEquals('<', $lookup->table['lt']); // expressed strangely in source file

        // symbol char
        static::assertEquals('θ', $lookup->table['theta']);
    }
}
