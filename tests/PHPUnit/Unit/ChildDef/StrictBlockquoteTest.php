<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ChildDef;

use HTMLPurifier\ChildDef\StrictBlockquote;

/**
 * Class StrictBlockquoteTest
 *
 * @package HTMLPurifier\Tests\Unit\ChildDef
 */
class StrictBlockquoteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new StrictBlockquote('div | p');
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
    public function testPreserveValidP(): void
    {
        $this->assertResult('<p>Valid</p>');
    }

    /**
     * @test
     */
    public function testPreserveValidDiv(): void
    {
        $this->assertResult('<div>Still valid</div>');
    }

    /**
     * @test
     */
    public function testWrapTextWithP(): void
    {
        $this->assertResult('Needs wrap', '<p>Needs wrap</p>');
    }

    /**
     * @test
     */
    public function testNoWrapForWhitespaceOrValidElements(): void
    {
        $this->assertResult('<p>Do not wrap</p>    <p>Whitespace</p>');
    }

    /**
     * @test
     */
    public function testWrapTextNextToValidElements(): void
    {
        $this->assertResult(
            'Wrap' . '<p>Do not wrap</p>',
            '<p>Wrap</p><p>Do not wrap</p>'
        );
    }

    /**
     * @test
     */
    public function testWrapInlineElements(): void
    {
        $this->assertResult(
            '<p>Do not</p>' . '<b>Wrap</b>',
            '<p>Do not</p><p><b>Wrap</b></p>'
        );
    }

    /**
     * @test
     */
    public function testWrapAndRemoveInvalidTags(): void
    {
        $this->assertResult(
            '<li>Not allowed</li>Paragraph.<p>Hmm.</p>',
            '<p>Not allowedParagraph.</p><p>Hmm.</p>'
        );
    }

    /**
     * @test
     */
    public function testWrapComplicatedSring(): void
    {
        $this->assertResult(
            $var = 'He said<br />perhaps<br />we should <b>nuke</b> them.',
            "<p>$var</p>"
        );
    }

    /**
     * @test
     */
    public function testWrapAndRemoveInvalidTagsComplex(): void
    {
        $this->assertResult(
            '<foo>Bar</foo><bas /><b>People</b>Conniving.' . '<p>Fools!</p>',
            '<p>Bar' . '<b>People</b>Conniving.</p><p>Fools!</p>'
        );
    }

    /**
     * @test
     */
    public function testAlternateWrapper(): void
    {
        $this->config->set('HTML.BlockWrapper', 'div');
        $this->assertResult('Needs wrap', '<div>Needs wrap</div>');
    }

    /**
     * @test
     */
    public function testError(): void
    {
        $this->expectError();
        $this->expectErrorMessage('Cannot use non-block element as block wrapper');
        $this->obj = new StrictBlockquote('div | p');
        $this->config->set('HTML.BlockWrapper', 'dav');
        $this->config->set('Cache.DefinitionImpl', null);
        $this->assertResult('Needs wrap', '<p>Needs wrap</p>');
    }
}
