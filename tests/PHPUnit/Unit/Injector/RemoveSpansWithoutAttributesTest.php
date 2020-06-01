<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Injector;

/**
 * Class RemoveSpansWithoutAttributesTest
 *
 * @package HTMLPurifier\Tests\Unit\Injector
 */
class RemoveSpansWithoutAttributesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('HTML.Allowed', 'span[class],div,p,strong,em');
        $this->config->set('AutoFormat.RemoveSpansWithoutAttributes', true);
    }

    /**
     * @test
     */
    public function testSingleSpan(): void
    {
        $this->assertResult(
            '<span>foo</span>',
            'foo'
        );
    }

    /**
     * @test
     */
    public function testSingleSpanWithAttributes(): void
    {
        $this->assertResult(
            '<span class="bar">foo</span>',
            '<span class="bar">foo</span>'
        );
    }

    /**
     * @test
     */
    public function testSingleNestedSpan(): void
    {
        $this->assertResult(
            '<p><span>foo</span></p>',
            '<p>foo</p>'
        );
    }

    /**
     * @test
     */
    public function testSingleNestedSpanWithAttributes(): void
    {
        $this->assertResult(
            '<p><span class="bar">foo</span></p>',
            '<p><span class="bar">foo</span></p>'
        );
    }

    /**
     * @test
     */
    public function testSpanWithChildren(): void
    {
        $this->assertResult(
            '<span>foo <strong>bar</strong> <em>baz</em></span>',
            'foo <strong>bar</strong> <em>baz</em>'
        );
    }

    /**
     * @test
     */
    public function testSpanWithSiblings(): void
    {
        $this->assertResult(
            '<p>before <span>inside</span> <strong>after</strong></p>',
            '<p>before inside <strong>after</strong></p>'
        );
    }

    /**
     * @test
     */
    public function testNestedSpanWithSiblingsAndChildren(): void
    {
        $this->assertResult(
            '<p>a <span>b <em>c</em> d</span> e</p>',
            '<p>a b <em>c</em> d e</p>'
        );
    }

    /**
     * @test
     */
    public function testNestedSpansWithoutAttributes(): void
    {
        $this->assertResult(
            '<span>one<span>two<span>three</span></span></span>',
            'onetwothree'
        );
    }

    /**
     * @test
     */
    public function testDeeplyNestedSpan(): void
    {
        $this->assertResult(
            '<div><div><div><span class="a">a <span>b</span> c</span></div></div></div>',
            '<div><div><div><span class="a">a b c</span></div></div></div>'
        );
    }

    /**
     * @test
     */
    public function testSpanWithInvalidAttributes(): void
    {
        $this->assertResult(
            '<p><span snorkel buzzer="emu">foo</span></p>',
            '<p>foo</p>'
        );
    }

    /**
     * @test
     */
    public function testNestedAlternateSpans(): void
    {
        $this->assertResult(
            '<span>a <span class="x">b <span>c <span class="y">d <span>e <span class="z">f
</span></span></span></span></span></span>',
            'a <span class="x">b c <span class="y">d e <span class="z">f
</span></span></span>'
        );
    }

    /**
     * @test
     */
    public function testSpanWithSomeInvalidAttributes(): void
    {
        $this->assertResult(
            '<p><span buzzer="emu" class="bar">foo</span></p>',
            '<p><span class="bar">foo</span></p>'
        );
    }
}
