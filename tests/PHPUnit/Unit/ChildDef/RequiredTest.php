<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ChildDef;

use HTMLPurifier\ChildDef\Required;

/**
 * Class RequiredTest
 *
 * @package HTMLPurifier\Tests\Unit\ChildDef
 */
class RequiredTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Required('dt | dd');
    }

    /**
     * @test
     */
    public function testPrepareString(): void
    {
        $def = new Required('foobar | bang |gizmo');
        static::assertEquals(
            [
                'foobar' => true,
                'bang' => true,
                'gizmo' => true
            ],
            $def->elements
        );
    }

    /**
     * @test
     */
    public function testPrepareArray(): void
    {
        $def = new Required(['href', 'src']);
        static::assertEquals(
            [
                'href' => true,
                'src' => true
            ],
            $def->elements
        );
    }

    /**
     * @test
     */
    public function testEmptyInput()
    {
        $this->assertResult('', false);
    }

    /**
     * @test
     */
    public function testRemoveIllegalTagsAndElements(): void
    {
        $this->assertResult(
            '<dt>Term</dt>Text in an illegal location' .
            '<dd>Definition</dd><b>Illegal tag</b>',
            '<dt>Term</dt><dd>Definition</dd>'
        );
        $this->assertResult('How do you do!', false);
    }

    /**
     * @test
     */
    public function testIgnoreWhitespace(): void
    {
        // whitespace shouldn't trigger it
        $this->assertResult("\n<dd>Definition</dd>       ");
    }

    /**
     * @test
     */
    public function testPreserveWhitespaceAfterRemoval(): void
    {
        $this->assertResult(
            '<dd>Definition</dd>       <b></b>       ',
            '<dd>Definition</dd>              '
        );
    }

    /**
     * @test
     */
    public function testDeleteNodeIfOnlyWhitespace(): void
    {
        $this->assertResult("\t      ", false);
    }

    /**
     * @test
     */
    public function testPCDATAAllowed(): void
    {
        $this->obj = new Required('#PCDATA | b');
        $this->assertResult('Out <b>Bold text</b><img />', 'Out <b>Bold text</b>');
    }

    /**
     * @test
     */
    public function testPCDATAAllowedJump(): void
    {
        $this->obj = new Required('#PCDATA | b');
        $this->assertResult('A <i>foo</i>', 'A foo');
    }
}
