<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Token\Start;
use HTMLPurifier\TokenFactory;

/**
 * Class TokenFactoryTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class TokenFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $factory = new TokenFactory();

        $regular = new Start('a', ['href' => 'about:blank']);
        $generated = $factory->createStart('a', ['href' => 'about:blank']);

        static::assertEquals($regular, $generated);
    }
}
