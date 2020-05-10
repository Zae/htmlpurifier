<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\StringHash;

/**
 * Class StringHashTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class StringHashTest extends TestCase
{
    /**
     * @test
     */
    public function testUsed(): void
    {
        $hash = new StringHash([
            'key' => 'value',
            'key2' => 'value2'
        ]);
        static::assertEquals([], $hash->getAccessed());

        $t = $hash->offsetGet('key');
        static::assertEquals(['key' => true], $hash->getAccessed());

        $hash->resetAccessed();
        static::assertEquals([], $hash->getAccessed());
    }
}
