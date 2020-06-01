<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Filter;

use HTMLPurifier\HTMLPurifier;
use HTMLPurifier\Tests\Unit\TestCase;

/**
 * Class ExtractStyleBlocksTest
 *
 * @package HTMLPurifier\Tests\Unit\Filter
 * @group extractstyleblocks
 */
class ExtractStyleBlocksTest extends TestCase
{
    // usual use case:

    protected function setUp(): void
    {
        if (!class_exists(\HTMLPurifier_Filter_ExtractStyleBlocks::class)) {
            static::markTestSkipped('ExtractStyleBlocks not loaded.');
        }

        if (!class_exists('csstidy')) {
            static::markTestSkipped('csstidy does not exist');
        }

        parent::setUp();
    }

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function test_tokenizeHTML_extractStyleBlocks(): void
    {
        $this->config->set('Filter.ExtractStyleBlocks', true);
        $purifier = new HTMLPurifier($this->config);
        $result = $purifier->purify('<style type="text/css">.foo {text-align:center;bogus:remove-me;} body.class[foo="attr"] {text-align:right;}</style>Test<style>* {font-size:12pt;}</style>');

        static::assertEquals('Test', $result);
        static::assertEquals(
            [
                ".foo {\ntext-align:center\n}",
                "* {\nfont-size:12pt\n}"
            ],
            $purifier->context->get('StyleBlocks')
        );
    }

    /**
     * @param       $html
     * @param bool  $expect
     * @param array $styles
     *
     * @throws \HTMLPurifier\Exception
     */
    private function assertExtractStyleBlocks($html, $expect = true, $styles = []): void
    {
        $filter = new \HTMLPurifier_Filter_ExtractStyleBlocks(); // disable cleaning

        if ($expect === true) {
            $expect = $html;
        }

        $this->config->set('Filter.ExtractStyleBlocks.TidyImpl', false);
        $result = $filter->preFilter($html, $this->config, $this->context);

        static::assertEquals($expect, $result);
        static::assertEquals($styles, $this->context->get('StyleBlocks'));
    }

    /**
     * @test
     */
    public function test_extractStyleBlocks_preserve(): void
    {
        $this->assertExtractStyleBlocks('Foobar');
    }

    /**
     * @test
     */
    public function test_extractStyleBlocks_allStyle(): void
    {
        $this->assertExtractStyleBlocks('<style>foo</style>', '', ['foo']);
    }

    /**
     * @test
     */
    public function test_extractStyleBlocks_multipleBlocks(): void
    {
        $this->assertExtractStyleBlocks(
            "<style>1</style><style>2</style>NOP<style>4</style>",
            "NOP",
            ['1', '2', '4']
        );
    }

    /**
     * @test
     */
    public function test_extractStyleBlocks_blockWithAttributes(): void
    {
        $this->assertExtractStyleBlocks(
            '<style type="text/css">css</style>',
            '',
            ['css']
        );
    }

    /**
     * @test
     */
    public function test_extractStyleBlocks_styleWithPadding(): void
    {
        $this->assertExtractStyleBlocks(
            "Alas<styled>Awesome</styled>\n<style>foo</style> Trendy!",
            "Alas<styled>Awesome</styled>\n Trendy!",
            ['foo']
        );
    }

    /**
     * @param      $input
     * @param bool $expect
     *
     * @throws \HTMLPurifier\Exception
     */
    private function assertCleanCSS($input, $expect = true): void
    {
        $filter = new \HTMLPurifier_Filter_ExtractStyleBlocks();

        if ($expect === true) {
            $expect = $input;
        }

        $this->normalize($input);
        $this->normalize($expect);

        $result = $filter->cleanCSS($input, $this->config, $this->context);

        static::assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function test_cleanCSS_malformed(): void
    {
        $this->assertCleanCSS('</style>', '');
    }

    /**
     * @test
     */
    public function test_cleanCSS_selector(): void
    {
        $this->assertCleanCSS("a .foo #id div.cl#foo {\nfont-weight:700\n}");
    }

    /**
     * @test
     */
    public function test_cleanCSS_angledBrackets(): void
    {
        static::markTestIncomplete();
        // [Content] No longer can smuggle in angled brackets using
        // font-family; when we add support for 'content', reinstate
        // this test.
        //$this->assertCleanCSS(
        //    ".class {\nfont-family:'</style>';\n}",
        //    ".class {\nfont-family:\"\\3C /style\\3E \";\n}"
        //);
    }

    /**
     * @test
     */
    public function test_cleanCSS_angledBrackets2(): void
    {
        static::markTestIncomplete();
        // CSSTidy's behavior in this case is wrong, and should be fixed
        //$this->assertCleanCSS(
        //    "span[title=\"</style>\"] {\nfont-size:12pt;\n}",
        //    "span[title=\"\\3C /style\\3E \"] {\nfont-size:12pt;\n}"
        //);
    }

    /**
     * @test
     */
    public function test_cleanCSS_bogus(): void
    {
        $this->assertCleanCSS("div {bogus:tree}", "div {\n}");
    }

    /* [CONTENT]
    public function test_cleanCSS_escapeCodes()
    {
        $this->assertCleanCSS(
            ".class {\nfont-family:\"\\3C /style\\3E \";\n}"
        );
    }

    public function test_cleanCSS_noEscapeCodes()
    {
        $this->config->set('Filter.ExtractStyleBlocks.Escaping', false);
        $this->assertCleanCSS(
            ".class {\nfont-family:\"</style>\";\n}"
        );
    }
     */

    /**
     * @test
     */
    public function test_cleanCSS_scope(): void
    {
        $this->config->set('Filter.ExtractStyleBlocks.Scope', '#foo');
        $this->assertCleanCSS(
            "p {\ntext-indent:1em\n}",
            "#foo p {\ntext-indent:1em\n}"
        );
    }

    /**
     * @test
     */
    public function test_cleanCSS_scopeWithSelectorCommas(): void
    {
        $this->config->set('Filter.ExtractStyleBlocks.Scope', '#foo');
        $this->assertCleanCSS(
            "b, i {\ntext-decoration:underline\n}",
            "#foo b, #foo i {\ntext-decoration:underline\n}"
        );
    }

    /**
     * @test
     */
    public function test_cleanCSS_scopeWithNaughtySelector(): void
    {
        $this->config->set('Filter.ExtractStyleBlocks.Scope', '#foo');
        $this->assertCleanCSS("  + p {\ntext-indent:1em\n}", "#foo p {\ntext-indent:1em\n}");
    }

    /**
     * @test
     */
    public function test_cleanCSS_scopeWithMultipleNaughtySelectors(): void
    {
        $this->config->set('Filter.ExtractStyleBlocks.Scope', '#foo');
        $this->assertCleanCSS("  ++ ++ p {\ntext-indent:1em\n}", "#foo p {\ntext-indent:1em\n}");
    }

    /**
     * @test
     */
    public function test_cleanCSS_scopeWithCommas(): void
    {
        $this->config->set('Filter.ExtractStyleBlocks.Scope', '#foo, .bar');
        $this->assertCleanCSS(
            "p {\ntext-indent:1em\n}",
            "#foo p, .bar p {\ntext-indent:1em\n}"
        );
    }

    /**
     * @test
     */
    public function test_cleanCSS_scopeAllWithCommas(): void
    {
        $this->config->set('Filter.ExtractStyleBlocks.Scope', '#foo, .bar');
        $this->assertCleanCSS(
            "p, div {\ntext-indent:1em\n}",
            "#foo p, .bar p, #foo div, .bar div {\ntext-indent:1em\n}"
        );
    }

    /**
     * @test
     */
    public function test_cleanCSS_scopeWithConflicts(): void
    {
        $this->config->set('Filter.ExtractStyleBlocks.Scope', 'p');
        $this->assertCleanCSS(
            "div {
text-align:right
}

p div {
text-align:left
}",

            "p div {
text-align:right
}

p p div {
text-align:left
}"
        );
    }

    /**
     * @test
     */
    public function test_removeComments(): void
    {
        $this->assertCleanCSS(
            "<!--
div {
text-align:right
}
-->",
            "div {
text-align:right
}"
        );
    }

    /**
     * @test
     */
    public function test_atSelector(): void
    {
        $this->assertCleanCSS(
            "{
    b { text-align: center }
}",
            ""
        );
    }

    /**
     * @test
     */
    public function test_selectorValidation(): void
    {
        $this->assertCleanCSS(
            "&, & {
text-align: center
}",
            ""
        );
        $this->assertCleanCSS(
            "&, b {
text-align:center
}",
            "b {
text-align:center
}"
        );
        $this->assertCleanCSS(
            "& a #foo:hover.bar   +b > i {
text-align:center
}",
            "a #foo:hover.bar + b \\3E  i {
text-align:center
}"
        );
        $this->assertCleanCSS("doesnt-exist { text-align:center }", "");
    }

    /**
     * @test
     */
    public function test_cleanCSS_caseSensitive(): void
    {
        $this->assertCleanCSS("a .foo #ID div.cl#foo {\nbackground:url(\"http://foo/BAR\")\n}");
    }

    /**
     * @test
     */
    public function test_extractStyleBlocks_backtracking(): void
    {
        $goo = str_repeat("a", 1000000); // 1M to trigger, sometimes it's less!
        $this->assertExtractStyleBlocks("<style></style>" . $goo, $goo, ['']);
    }
}
