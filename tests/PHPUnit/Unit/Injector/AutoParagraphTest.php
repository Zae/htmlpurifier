<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Injector;

/**
 * Class AutoParagraphTest
 *
 * @package HTMLPurifier\Tests\Unit\Injector
 */
class AutoParagraphTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('AutoFormat.AutoParagraph', true);
    }

    /**
     * @test
     */
    public function testSingleParagraph(): void
    {
        $this->assertResult(
            'Foobar',
            '<p>Foobar</p>'
        );
    }

    /**
     * @test
     */
    public function testSingleMultiLineParagraph(): void
    {
        $this->assertResult(
            'Par 1
Par 1 still',
            '<p>Par 1
Par 1 still</p>'
        );
    }

    /**
     * @test
     */
    public function testTwoParagraphs(): void
    {
        $this->assertResult(
            'Par1

Par2',
            "<p>Par1</p>

<p>Par2</p>"
        );
    }

    /**
     * @test
     */
    public function testTwoParagraphsWithLotsOfSpace(): void
    {
        $this->assertResult(
            'Par1



Par2',
            '<p>Par1</p>

<p>Par2</p>'
        );
    }

    /**
     * @test
     */
    public function testTwoParagraphsWithInlineElements(): void
    {
        $this->assertResult(
            '<b>Par1</b>

<i>Par2</i>',
            '<p><b>Par1</b></p>

<p><i>Par2</i></p>'
        );
    }

    /**
     * @test
     */
    public function testSingleParagraphThatLooksLikeTwo(): void
    {
        $this->assertResult(
            '<b>Par1

Par2</b>',
            '<p><b>Par1

Par2</b></p>'
        );
    }

    /**
     * @test
     */
    public function testAddParagraphAdjacentToParagraph(): void
    {
        $this->assertResult(
            'Par1<p>Par2</p>',
            '<p>Par1</p>

<p>Par2</p>'
        );
    }

    /**
     * @test
     */
    public function testParagraphUnclosedInlineElement(): void
    {
        $this->assertResult(
            '<b>Par1',
            '<p><b>Par1</b></p>'
        );
    }

    /**
     * @test
     */
    public function testPreservePreTags(): void
    {
        $this->assertResult(
            '<pre>Par1

Par1</pre>'
        );
    }

    /**
     * @test
     */
    public function testIgnoreTrailingWhitespace(): void
    {
        $this->assertResult(
            'Par1

  ',
            '<p>Par1</p>

'
        );
    }

    /**
     * @test
     */
    public function testDoNotParagraphBlockElements(): void
    {
        $this->assertResult(
            'Par1

<div>Par2</div>

Par3',
            '<p>Par1</p>

<div>Par2</div>

<p>Par3</p>'
        );
    }

    /**
     * @test
     */
    public function testParagraphTextAndInlineNodes(): void
    {
        $this->assertResult(
            'Par<b>1</b>',
            '<p>Par<b>1</b></p>'
        );
    }

    /**
     * @test
     */
    public function testPreserveLeadingWhitespace(): void
    {
        $this->assertResult(
            '

Par',
            '

<p>Par</p>'
        );
    }

    /**
     * @test
     */
    public function testPreserveSurroundingWhitespace(): void
    {
        $this->assertResult(
            '

Par

',
            '

<p>Par</p>

'
        );
    }

    /**
     * @test
     */
    public function testParagraphInsideBlockNode(): void
    {
        $this->assertResult(
            '<div>Par1

Par2</div>',
            '<div><p>Par1</p>

<p>Par2</p></div>'
        );
    }

    /**
     * @test
     */
    public function testParagraphInlineNodeInsideBlockNode(): void
    {
        $this->assertResult(
            '<div><b>Par1</b>

Par2</div>',
            '<div><p><b>Par1</b></p>

<p>Par2</p></div>'
        );
    }

    /**
     * @test
     */
    public function testNoParagraphWhenOnlyOneInsideBlockNode(): void
    {
        $this->assertResult('<div>Par1</div>');
    }

    /**
     * @test
     */
    public function testParagraphTwoInlineNodesInsideBlockNode(): void
    {
        $this->assertResult(
            '<div><b>Par1</b>

<i>Par2</i></div>',
            '<div><p><b>Par1</b></p>

<p><i>Par2</i></p></div>'
        );
    }

    /**
     * @test
     */
    public function testPreserveInlineNodesInPreTag(): void
    {
        $this->assertResult(
            '<pre><b>Par1</b>

<i>Par2</i></pre>'
        );
    }

    /**
     * @test
     */
    public function testSplitUpInternalsOfPTagInBlockNode(): void
    {
        $this->assertResult(
            '<div><p>Foo

Bar</p></div>',
            '<div><p>Foo</p>

<p>Bar</p></div>'
        );
    }

    /**
     * @test
     */
    public function testSplitUpInlineNodesInPTagInBlockNode(): void
    {
        $this->assertResult(
            '<div><p><b>Foo</b>

<i>Bar</i></p></div>',
            '<div><p><b>Foo</b></p>

<p><i>Bar</i></p></div>'
        );
    }

    /**
     * @test
     */
    public function testNoParagraphSingleInlineNodeInBlockNode(): void
    {
        $this->assertResult('<div><b>Foo</b></div>');
    }

    /**
     * @test
     */
    public function testParagraphInBlockquote(): void
    {
        $this->assertResult(
            '<blockquote>Par1

Par2</blockquote>',
            '<blockquote><p>Par1</p>

<p>Par2</p></blockquote>'
        );
    }

    /**
     * @test
     */
    public function testNoParagraphBetweenListItem(): void
    {
        $this->assertResult(
            '<ul><li>Foo</li>

<li>Bar</li></ul>'
        );
    }

    /**
     * @test
     */
    public function testParagraphSingleElementWithSurroundingSpace(): void
    {
        $this->assertResult(
            '<div>

Bar

</div>',
            '<div>

<p>Bar</p>

</div>'
        );
    }

    /**
     * @test
     */
    public function testIgnoreExtraSpaceWithLeadingInlineNode(): void
    {
        $this->assertResult(
            '<b>Par1</b>a



Par2',
            '<p><b>Par1</b>a</p>

<p>Par2</p>'
        );
    }

    /**
     * @test
     */
    public function testAbsorbExtraEndingPTag(): void
    {
        $this->assertResult(
            'Par1

Par2</p>',
            '<p>Par1</p>

<p>Par2</p>'
        );
    }

    /**
     * @test
     */
    public function testAbsorbExtraEndingDivTag(): void
    {
        $this->assertResult(
            'Par1

Par2</div>',
            '<p>Par1</p>

<p>Par2</p>'
        );
    }

    /**
     * @test
     */
    public function testDoNotParagraphSingleSurroundingSpaceInBlockNode(): void
    {
        $this->assertResult(
            '<div>
Par1
</div>'
        );
    }

    /**
     * @test
     */
    public function testBlockNodeTextDelimeterInBlockNode(): void
    {
        $this->assertResult(
            '<div>Par1

<div>Par2</div></div>',
            '<div><p>Par1</p>

<div>Par2</div></div>'
        );
    }

    /**
     * @test
     */
    public function testBlockNodeTextDelimeterWithoutDoublespaceInBlockNode(): void
    {
        $this->assertResult(
            '<div>Par1
<div>Par2</div></div>'
        );
    }

    /**
     * @test
     */
    public function testBlockNodeTextDelimeterWithoutDoublespace(): void
    {
        $this->assertResult(
            'Par1
<div>Par2</div>',
            '<p>Par1
</p>

<div>Par2</div>'
        );
    }

    /**
     * @test
     */
    public function testTwoParagraphsOfTextAndInlineNode(): void
    {
        $this->assertResult(
            'Par1

<b>Par2</b>',
            '<p>Par1</p>

<p><b>Par2</b></p>'
        );
    }

    /**
     * @test
     */
    public function testLeadingInlineNodeParagraph(): void
    {
        $this->assertResult(
            '<img /> Foo',
            '<p><img /> Foo</p>'
        );
    }

    /**
     * @test
     */
    public function testTrailingInlineNodeParagraph(): void
    {
        $this->assertResult(
            '<li>Foo <a>bar</a></li>'
        );
    }

    /**
     * @test
     */
    public function testTwoInlineNodeParagraph(): void
    {
        $this->assertResult(
            '<li><b>baz</b><a>bar</a></li>'
        );
    }

    /**
     * @test
     */
    public function testNoParagraphTrailingBlockNodeInBlockNode(): void
    {
        $this->assertResult(
            '<div><div>asdf</div><b>asdf</b></div>'
        );
    }

    /**
     * @test
     */
    public function testParagraphTrailingBlockNodeWithDoublespaceInBlockNode(): void
    {
        $this->assertResult(
            '<div><div>asdf</div>

<b>asdf</b></div>',
            '<div><div>asdf</div>

<p><b>asdf</b></p></div>'
        );
    }

    /**
     * @test
     */
    public function testParagraphTwoInlineNodesAndWhitespaceNode(): void
    {
        $this->assertResult(
            '<b>One</b> <i>Two</i>',
            '<p><b>One</b> <i>Two</i></p>'
        );
    }

    /**
     * @test
     */
    public function testNoParagraphWithInlineRootNode(): void
    {
        $this->config->set('HTML.Parent', 'span');
        $this->assertResult(
            'Par

Par2'
        );
    }

    /**
     * @test
     */
    public function testInlineAndBlockTagInDivNoParagraph(): void
    {
        $this->assertResult(
            '<div><code>bar</code> mmm <pre>asdf</pre></div>'
        );
    }

    /**
     * @test
     */
    public function testInlineAndBlockTagInDivNeedingParagraph(): void
    {
        $this->assertResult(
            '<div><code>bar</code> mmm

<pre>asdf</pre></div>',
            '<div><p><code>bar</code> mmm</p>

<pre>asdf</pre></div>'
        );
    }

    /**
     * @test
     */
    public function testTextInlineNodeTextThenDoubleNewlineNeedsParagraph(): void
    {
        $this->assertResult(
            '<div>asdf <code>bar</code> mmm

<pre>asdf</pre></div>',
            '<div><p>asdf <code>bar</code> mmm</p>

<pre>asdf</pre></div>'
        );
    }

    /**
     * @test
     */
    public function testUpcomingTokenHasNewline(): void
    {
        $this->assertResult(
            '<div>Test<b>foo</b>bar<b>bing</b>bang

boo</div>',
            '<div><p>Test<b>foo</b>bar<b>bing</b>bang</p>

<p>boo</p></div>'
        );
    }

    /**
     * @test
     */
    public function testEmptyTokenAtEndOfDiv(): void
    {
        $this->assertResult(
            '<div><p>foo</p>
</div>',
            '<div><p>foo</p>
</div>'
        );
    }

    /**
     * @test
     */
    public function testEmptyDoubleLineTokenAtEndOfDiv(): void
    {
        $this->assertResult(
            '<div><p>foo</p>

</div>',
            '<div><p>foo</p>

</div>'
        );
    }

    /**
     * @test
     */
    public function testTextState11Root(): void
    {
        $this->assertResult('<div></div>   ');
    }

    /**
     * @test
     */
    public function testTextState11Element()
    {
        $this->assertResult(
            "<div><div></div>

</div>");
    }

    /**
     * @test
     */
    public function testTextStateLikeElementState111NoWhitespace(): void
    {
        $this->assertResult('<div><p>P</p>Boo</div>', '<div><p>P</p>Boo</div>');
    }

    /**
     * @test
     */
    public function testElementState111NoWhitespace(): void
    {
        $this->assertResult('<div><p>P</p><b>Boo</b></div>', '<div><p>P</p><b>Boo</b></div>');
    }

    /**
     * @test
     */
    public function testElementState133(): void
    {
        $this->assertResult(
            "<div><b>B</b><pre>Ba</pre>

Bar</div>",
            "<div><b>B</b><pre>Ba</pre>

<p>Bar</p></div>"
        );
    }

    /**
     * @test
     */
    public function testElementState22(): void
    {
        $this->assertResult(
            '<ul><li>foo</li></ul>'
        );
    }

    /**
     * @test
     */
    public function testElementState311(): void
    {
        $this->assertResult(
            '<p>Foo</p><b>Bar</b>',
            '<p>Foo</p>

<p><b>Bar</b></p>'
        );
    }

    /**
     * @test
     */
    public function testAutoClose(): void
    {
        $this->assertResult(
            '<p></p>
<hr />'
        );
    }

    /**
     * @test
     */
    public function testErrorNeeded(): void
    {
        $this->config->set('HTML.Allowed', 'b');
        $this->expectError('Cannot enable AutoParagraph injector because p is not allowed');
        $this->assertResult('<b>foobar</b>');
    }

    /**
     * @test
     */
    public function testParentElement(): void
    {
        $this->config->set('HTML.Allowed', 'p,ul,li');
        $this->assertResult('Foo<ul><li>Bar</li></ul>', "<p>Foo</p>\n\n<ul><li>Bar</li></ul>");
    }
}
