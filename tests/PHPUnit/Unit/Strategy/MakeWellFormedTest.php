<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy;

use HTMLPurifier\Strategy\MakeWellFormed;

/**
 * Class MakeWellFormedTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy
 */
class MakeWellFormedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new MakeWellFormed();
    }

    /**
     * @test
     */
    public function testEmptyInput(): void
    {
        $this->assertResult('');
    }

    /**
     * @test
     */
    public function testWellFormedInput(): void
    {
        $this->assertResult('This is <b>bold text</b>.');
    }

    /**
     * @test
     */
    public function testUnclosedTagTerminatedByDocumentEnd(): void
    {
        $this->assertResult(
            '<b>Unclosed tag, gasp!',
            '<b>Unclosed tag, gasp!</b>'
        );
    }

    /**
     * @test
     */
    public function testUnclosedTagTerminatedByParentNodeEnd(): void
    {
        $this->assertResult(
            '<b><i>Bold and italic?</b>',
            '<b><i>Bold and italic?</i></b><i></i>'
        );
    }

    /**
     * @test
     */
    public function testRemoveStrayClosingTag(): void
    {
        $this->assertResult(
            'Unused end tags... recycle!</b>',
            'Unused end tags... recycle!'
        );
    }

    /**
     * @test
     */
    public function testConvertStartToEmpty(): void
    {
        $this->assertResult(
            '<br style="clear:both;">',
            '<br style="clear:both;" />'
        );
    }

    /**
     * @test
     */
    public function testConvertEmptyToStart(): void
    {
        $this->assertResult(
            '<div style="clear:both;" />',
            '<div style="clear:both;"></div>'
        );
    }

    /**
     * @test
     */
    public function testAutoCloseParagraph(): void
    {
        $this->assertResult(
            '<p>Paragraph 1<p>Paragraph 2',
            '<p>Paragraph 1</p><p>Paragraph 2</p>'
        );
    }

    /**
     * @test
     */
    public function testAutoCloseParagraphInsideDiv(): void
    {
        $this->assertResult(
            '<div><p>Paragraphs<p>In<p>A<p>Div</div>',
            '<div><p>Paragraphs</p><p>In</p><p>A</p><p>Div</p></div>'
        );
    }

    /**
     * @test
     */
    public function testAutoCloseListItem(): void
    {
        $this->assertResult(
            '<ol><li>Item 1<li>Item 2</ol>',
            '<ol><li>Item 1</li><li>Item 2</li></ol>'
        );
    }

    /**
     * @test
     */
    public function testAutoCloseColgroup(): void
    {
        $this->assertResult(
            '<table><colgroup><col /><tr></tr></table>',
            '<table><colgroup><col /></colgroup><tr></tr></table>'
        );
    }

    /**
     * @test
     */
    public function testAutoCloseMultiple(): void
    {
        $this->assertResult(
            '<b><span><div></div>asdf',
            '<b><span></span></b><div><b></b></div><b>asdf</b>'
        );
    }

    /**
     * @test
     */
    public function testUnrecognized(): void
    {
        $this->assertResult(
            '<asdf><foobar /><biddles>foo</asdf>',
            '<asdf><foobar /><biddles>foo</biddles></asdf>'
        );
    }

    /**
     * @test
     */
    public function testBlockquoteWithInline(): void
    {
        $this->config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $this->assertResult(
        // This is actually invalid, but will be fixed by
        // ChildDef_StrictBlockquote
            '<blockquote>foo<b>bar</b></blockquote>'
        );
    }

    /**
     * @test
     */
    public function testLongCarryOver(): void
    {
        $this->assertResult(
            '<b>asdf<div>asdf<i>df</i></div>asdf</b>',
            '<b>asdf</b><div><b>asdf<i>df</i></b></div><b>asdf</b>'
        );
    }

    /**
     * @test
     */
    public function testInterleaved(): void
    {
        $this->assertResult(
            '<u>foo<i>bar</u>baz</i>',
            '<u>foo<i>bar</i></u><i>baz</i>'
        );
    }

    /**
     * @test
     */
    public function testNestedOl(): void
    {
        $this->assertResult(
            '<ol><ol><li>foo</li></ol></ol>',
            '<ol><ol><li>foo</li></ol></ol>'
        );
    }

    /**
     * @test
     */
    public function testNestedUl(): void
    {
        $this->assertResult(
            '<ul><ul><li>foo</li></ul></ul>',
            '<ul><ul><li>foo</li></ul></ul>'
        );
    }

    /**
     * @test
     */
    public function testNestedOlWithStrangeEnding(): void
    {
        $this->assertResult(
            '<ol><li><ol><ol><li>foo</li></ol></li><li>foo</li></ol>',
            '<ol><li><ol><ol><li>foo</li></ol></ol></li><li>foo</li></ol>'
        );
    }

    /**
     * @test
     */
    public function testNoAutocloseIfNoParentsCanAccomodateTag(): void
    {
        $this->assertResult(
            '<table><tr><td><li>foo</li></td></tr></table>',
            '<table><tr><td>foo</td></tr></table>'
        );
    }
}
