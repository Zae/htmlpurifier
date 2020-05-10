<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Zipper;

/**
 * Class ZipperTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class ZipperTest extends TestCase
{
    /**
     * @test
     */
    public function testBasicNavigation(): void
    {
        [$z, $t] = Zipper::fromArray([0, 1, 2, 3]);
        static::assertEquals(0, $t);

        $t = $z->next($t);
        static::assertEquals(1, $t);

        $t = $z->prev($t);
        static::assertEquals(0, $t);

        $t = $z->advance($t, 2);
        static::assertEquals(2, $t);

        $t = $z->delete();
        static::assertEquals(3, $t);

        $z->insertBefore(4);
        $z->insertAfter(5);
        static::assertEquals([0, 1, 4, 3, 5], $z->toArray($t));

        [$old, $t] = $z->splice($t, 2, [6, 7]);

        static::assertEquals([3, 5], $old);
        static::assertEquals(6, $t);
        static::assertEquals([0, 1, 4, 6, 7], $z->toArray($t));
    }

    // ToDo: QuickCheck style test comparing with array_splice
}
