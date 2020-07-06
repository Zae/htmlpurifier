<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\Doctype;
use HTMLPurifier\DoctypeRegistry;
use HTMLPurifier\Exception;

/**
 * Class DoctypeRegistryTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class DoctypeRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function test_register(): void
    {
        $registry = new DoctypeRegistry();

        $d = $registry->register(
            $name = 'XHTML 1.0 Transitional',
            $xml = true,
            $modules = ['module-one', 'module-two'],
            $tidyModules = ['lenient-module'],
            $aliases = ['X10T']
        );

        $d2 = new Doctype($name, $xml, $modules, $tidyModules, $aliases);

        static::assertEquals($d, $d2);
        static::assertEquals($d, $registry->get('XHTML 1.0 Transitional'));

        // test shorthand
        $d = $registry->register(
            $name = 'XHTML 1.0 Strict', true, 'module', 'Tidy', 'X10S'
        );
        $d2 = new Doctype($name, true, ['module'], ['Tidy'], ['X10S']);

        static::assertEquals($d, $d2);
    }

    /**
     * @test
     */
    public function test_get(): void
    {
        // see also alias and register tests

        $registry = new DoctypeRegistry();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Doctype XHTML 2.0 does not exist');
        $registry->get('XHTML 2.0');

        // prevent XSS
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Doctype &lt;foo&gt; does not exist');
        $registry->get('<foo>');
    }

    /**
     * @test
     */
    public function testAliases(): void
    {
        $registry = new DoctypeRegistry();

        $d1 = $registry->register('Doc1', true, [], [], ['1']);

        static::assertEquals($d1, $registry->get('Doc1'));
        static::assertEquals($d1, $registry->get('1'));

        $d2 = $registry->register('Doc2', true, [], [], ['2']);

        static::assertEquals($d2, $registry->get('Doc2'));
        static::assertEquals($d2, $registry->get('2'));

        $d3 = $registry->register('1', true, [], [], []);

        // literal name overrides alias
        static::assertEquals($d3, $registry->get('1'));

        $d4 = $registry->register('One', true, [], [], ['1']);

        static::assertEquals($d4, $registry->get('One'));
        // still it overrides
        static::assertEquals($d3, $registry->get('1'));
    }
}
