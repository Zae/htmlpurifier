<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\PropertyList;

/**
 * Class PropertyListTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class PropertyListTest extends TestCase
{
    /**
     * @test
     */
    public function testBasic(): void
    {
        $plist = new PropertyList();
        $plist->set('key', 'value');

        static::assertEquals('value', $plist->get('key'));
    }

    public function testNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Key 'key' not found");

        $plist = new PropertyList();
        $plist->get('key');
    }

    public function testRecursion(): void
    {
        $parent_plist = new PropertyList();
        $parent_plist->set('key', 'value');
        $plist = new PropertyList();
        $plist->setParent($parent_plist);

        static::assertEquals('value', $plist->get('key'));
    }

    /**
     * @test
     */
    public function testOverride(): void
    {
        $parent_plist = new PropertyList();
        $parent_plist->set('key', 'value');
        $plist = new PropertyList();
        $plist->setParent($parent_plist);
        $plist->set('key',  'value2');

        static::assertEquals('value2', $plist->get('key'));
    }

    /**
     * @test
     */
    public function testRecursionNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Key 'key' not found");

        $parent_plist = new PropertyList();
        $plist = new PropertyList();
        $plist->setParent($parent_plist);

        static::assertEquals('value', $plist->get('key'));
    }

    /**
     * @test
     */
    public function testHas(): void
    {
        $plist = new PropertyList();
        static::assertEquals(false, $plist->has('key'));

        $plist->set('key', 'value');
        static::assertEquals(true, $plist->has('key'));
    }

    /**
     * @test
     */
    public function testReset(): void
    {
        $plist = new PropertyList();
        $plist->set('key1', 'value');
        $plist->set('key2', 'value');
        $plist->set('key3', 'value');

        static::assertEquals(true, $plist->has('key1'));
        static::assertEquals(true, $plist->has('key2'));
        static::assertEquals(true, $plist->has('key3'));

        $plist->reset('key2');
        static::assertEquals(true, $plist->has('key1'));
        static::assertEquals(false, $plist->has('key2'));
        static::assertEquals(true, $plist->has('key3'));

        $plist->reset();
        static::assertEquals(false, $plist->has('key1'));
        static::assertEquals(false, $plist->has('key2'));
        static::assertEquals(false, $plist->has('key3'));
    }

    /**
     * @test
     */
    public function testSquash(): void
    {
        $parent = new PropertyList();
        $parent->set('key1', 'hidden');
        $parent->set('key2', 2);
        $plist = new PropertyList($parent);
        $plist->set('key1', 1);
        $plist->set('key3', 3);

        static::assertEquals(
            ['key1' => 1, 'key2' => 2, 'key3' => 3],
            $plist->squash()
        );

        // updates don't show up...
        $plist->set('key2', 22);
        static::assertEquals(
            ['key1' => 1, 'key2' => 2, 'key3' => 3],
            $plist->squash()
        );

        // until you force
        static::assertEquals(
            ['key1' => 1, 'key2' => 22, 'key3' => 3],
            $plist->squash(true)
        );
    }
}
