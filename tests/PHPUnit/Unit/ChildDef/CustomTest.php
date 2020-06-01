<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ChildDef;

use HTMLPurifier\ChildDef\Custom;

/**
 * Class CustomTest
 *
 * @package HTMLPurifier\Tests\Unit\ChildDef
 */
class CustomTest extends TestCase
{
    /**
     * @test
     */
    public function test(): void
    {
        $this->obj = new Custom('(a,b?,c*,d+,(a,b)*)');

        static::assertEquals(
            ['a' => true, 'b' => true, 'c' => true, 'd' => true],
            $this->obj->elements
        );

        $this->assertResult('', false);
        $this->assertResult('<a /><a />', false);

        $this->assertResult('<a /><b /><c /><d /><a /><b />');
        $this->assertResult('<a /><d>Dob</d><a /><b>foo</b><a href="moo" /><b>foo</b>');
    }

    /**
     * @test
     */
    public function testNesting(): void
    {
        $this->obj = new Custom('(a,b,(c|d))+');

        static::assertEquals(
            ['a' => true, 'b' => true, 'c' => true, 'd' => true],
            $this->obj->elements
        );
        $this->assertResult('', false);
        $this->assertResult('<a /><b /><c /><a /><b /><d />');
        $this->assertResult('<a /><b /><c /><d />', false);
    }

    /**
     * @test
     */
    public function testNestedEitherOr(): void
    {
        $this->obj = new Custom('b,(a|(c|d))+');
        static::assertEquals(
            ['a' => true, 'b' => true, 'c' => true, 'd' => true],
            $this->obj->elements
        );
        $this->assertResult('', false);
        $this->assertResult('<b /><a /><c /><d />');
        $this->assertResult('<b /><d /><a /><a />');
        $this->assertResult('<b /><a />');
        $this->assertResult('<acd />', false);
    }

    /**
     * @test
     */
    public function testNestedQuantifier(): void
    {
        $this->obj = new Custom('(b,c+)*');
        static::assertEquals(['b' => true, 'c' => true], $this->obj->elements);
        $this->assertResult('');
        $this->assertResult('<b /><c />');
        $this->assertResult('<b /><c /><c /><c />');
        $this->assertResult('<b /><c /><b /><c />');
        $this->assertResult('<b /><c /><b />', false);
    }

    /**
     * @test
     */
    public function testEitherOr(): void
    {
        $this->obj = new Custom('a|b');
        static::assertEquals(['a' => true, 'b' => true], $this->obj->elements);
        $this->assertResult('', false);
        $this->assertResult('<a />');
        $this->assertResult('<b />');
        $this->assertResult('<a /><b />', false);
    }

    /**
     * @test
     */
    public function testCommafication(): void
    {
        $this->obj = new Custom('a,b');
        static::assertEquals(['a' => true, 'b' => true], $this->obj->elements);
        $this->assertResult('<a /><b />');
        $this->assertResult('<ab />', false);
    }

    /**
     * @test
     */
    public function testPcdata(): void
    {
        $this->obj = new Custom('#PCDATA,a');
        static::assertEquals(['#PCDATA' => true, 'a' => true], $this->obj->elements);
        $this->assertResult('foo<a />');
        $this->assertResult('<a />', false);
    }

    /**
     * @test
     */
    public function testWhitespace(): void
    {
        $this->obj = new Custom('a');
        static::assertEquals(['a' => true], $this->obj->elements);
        $this->assertResult('foo<a />', false);
        $this->assertResult('<a />');
        $this->assertResult('   <a />');
    }
}
