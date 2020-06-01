<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy;

use HTMLPurifier\Strategy\RemoveForeignElements;

/**
 * Class RemoveForeignElementsTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy
 */
class RemoveForeignElementsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new RemoveForeignElements();
    }

    /**
     * @test
     */
    public function testBlankInput(): void
    {
        $this->assertResult('');
    }

    /**
     * @test
     */
    public function testPreserveRecognizedElements(): void
    {
        $this->assertResult('This is <b>bold text</b>.');
    }

    /**
     * @test
     */
    public function testRemoveForeignElements(): void
    {
        $this->assertResult(
            '<asdf>Bling</asdf><d href="bang">Bong</d><foobar />',
            'BlingBong'
        );
    }

    /**
     * @test
     */
    public function testRemoveScriptAndContents(): void
    {
        $this->assertResult(
            '<script>alert();</script>',
            ''
        );
    }

    /**
     * @test
     */
    public function testRemoveStyleAndContents(): void
    {
        $this->assertResult(
            '<style>.foo {blink;}</style>',
            ''
        );
    }

    /**
     * @test
     */
    public function testRemoveOnlyScriptTagsLegacy(): void
    {
        $this->config->set('Core.RemoveScriptContents', false);
        $this->assertResult(
            '<script>alert();</script>',
            'alert();'
        );
    }

    /**
     * @test
     */
    public function testRemoveOnlyScriptTags(): void
    {
        $this->config->set('Core.HiddenElements', []);
        $this->assertResult(
            '<script>alert();</script>',
            'alert();'
        );
    }

    /**
     * @test
     */
    public function testRemoveInvalidImg(): void
    {
        $this->assertResult('<img />', '');
    }

    /**
     * @test
     */
    public function testPreserveValidImg(): void
    {
        $this->assertResult('<img src="foobar.gif" alt="foobar.gif" />');
    }

    /**
     * @test
     */
    public function testPreserveInvalidImgWhenRemovalIsDisabled(): void
    {
        $this->config->set('Core.RemoveInvalidImg', false);
        $this->assertResult('<img />');
    }

    /**
     * @test
     */
    public function testTextifyCommentedScriptContents(): void
    {
        $this->config->set('HTML.Trusted', true);
        $this->config->set('Output.CommentScriptContents', false); // simplify output
        $this->assertResult(
            '<script type="text/javascript"><!--
alert(<b>bold</b>);
// --></script>',
            '<script type="text/javascript">
alert(&lt;b&gt;bold&lt;/b&gt;);
// </script>'
        );
    }

    /**
     * @test
     */
    public function testRequiredAttributesTestNotPerformedOnEndTag(): void
    {
        $def = $this->config->getHTMLDefinition(true);
        $def->addElement('f', 'Block', 'Optional: #PCDATA', false, array('req*' => 'Text'));
        $this->assertResult('<f req="text">Foo</f> Bar');
    }

    /**
     * @test
     */
    public function testPreserveCommentsWithHTMLTrusted(): void
    {
        $this->config->set('HTML.Trusted', true);
        $this->assertResult('<!-- foo -->');
    }

    /**
     * @test
     */
    public function testRemoveTrailingHyphensInComment(): void
    {
        $this->config->set('HTML.Trusted', true);
        $this->assertResult('<!-- foo ----->', '<!-- foo -->');
    }

    /**
     * @test
     */
    public function testCollapseDoubleHyphensInComment(): void
    {
        $this->config->set('HTML.Trusted', true);
        $this->assertResult('<!-- bo --- asdf--as -->', '<!-- bo - asdf-as -->');
    }

    /**
     * @test
     */
    public function testPreserveCommentsWithLookup(): void
    {
        $this->config->set('HTML.AllowedComments', ['allowed']);
        $this->assertResult('<!-- allowed --><!-- not allowed -->', '<!-- allowed -->');
    }

    /**
     * @test
     */
    public function testPreserveCommentsWithRegexp(): void
    {
        $this->config->set('HTML.AllowedCommentsRegexp', '/^allowed[1-9]$/');
        $this->assertResult('<!-- allowed1 --><!-- not allowed -->', '<!-- allowed1 -->');
    }
}
