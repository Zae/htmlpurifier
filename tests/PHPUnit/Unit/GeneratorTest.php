<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use HTMLPurifier\EntityLookup;
use HTMLPurifier\Generator;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Token\Text;
use TypeError;

/**
 * Class GeneratorTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class GeneratorTest extends TestCase
{
    /**
     * Entity lookup table to help for a few tests.
     */
    private $_entity_lookup;

    public function setUp(): void
    {
        $this->_entity_lookup = EntityLookup::instance();
        parent::setUp();
        $this->config->set('Output.Newline', "\n");
    }

    /**
     * Creates a generator based on config and context member variables.
     */
    private function createGenerator(): Generator
    {
        return new Generator($this->config, $this->context);
    }

    /**
     * @param $token
     * @param $html
     */
    private function assertGenerateFromToken($token, $html): void
    {
        $generator = $this->createGenerator();
        $result = $generator->generateFromToken($token);
        
        static::assertEquals($html, $result);
    }

    /**
     * @test
     */
    public function test_generateFromToken_text(): void
    {
        $this->assertGenerateFromToken(
            new Text('Foobar.<>'),
            'Foobar.&lt;&gt;'
        );
    }

    /**
     * @test
     */
    public function test_generateFromToken_startWithAttr(): void
    {
        $this->assertGenerateFromToken(
            new Start('a',
                ['href' => 'dyn?a=foo&b=bar']
            ),
            '<a href="dyn?a=foo&amp;b=bar">'
        );
    }

    /**
     * @test
     */
    public function test_generateFromToken_end(): void
    {
        $this->assertGenerateFromToken(
            new End('b'),
            '</b>'
        );
    }

    /**
     * @test
     */
    public function test_generateFromToken_emptyWithAttr(): void
    {
        $this->assertGenerateFromToken(
            new EmptyToken('br',
                array('style' => 'font-family:"Courier New";')
            ),
            '<br style="font-family:&quot;Courier New&quot;;" />'
        );
    }

    /**
     * @test
     */
    public function test_generateFromToken_startNoAttr(): void
    {
        $this->assertGenerateFromToken(
            new Start('asdf'),
            '<asdf>'
        );
    }

    /**
     * @test
     */
    public function test_generateFromToken_emptyNoAttr(): void
    {
        $this->assertGenerateFromToken(
            new EmptyToken('br'),
            '<br />'
        );
    }

    /**
     * @test
     */
    public function test_generateFromToken_error(): void
    {
        $this->expectException(TypeError::class);
        $this->assertGenerateFromToken(null, '');
    }

    /**
     * @test
     */
    public function test_generateFromToken_unicode(): void
    {
        $theta_char = $this->_entity_lookup->table['theta'];
        $this->assertGenerateFromToken(
            new Text($theta_char),
            $theta_char
        );
    }

    /**
     * @test
     */
    public function test_generateFromToken_backtick(): void
    {
        $this->assertGenerateFromToken(
            new Start('img', ['alt' => '`foo']),
            '<img alt="`foo ">'
        );
    }

    /**
     * @test
     */
    public function test_generateFromToken_backtickDisabled(): void
    {
        $this->config->set('Output.FixInnerHTML', false);
        $this->assertGenerateFromToken(
            new Start('img', ['alt' => '`']),
            '<img alt="`">'
        );
    }

    /**
     * @test
     */
    public function test_generateFromToken_backtickNoChange(): void
    {
        $this->assertGenerateFromToken(
            new Start('img', ['alt' => '`foo` bar']),
            '<img alt="`foo` bar">'
        );
    }

    /**
     * @param        $attr
     * @param        $expect
     * @param string $element
     */
    private function assertGenerateAttributes($attr, $expect, $element = ''): void
    {
        $generator = $this->createGenerator();
        $result = $generator->generateAttributes($attr, $element);

        static::assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function test_generateAttributes_blank(): void
    {
        $this->assertGenerateAttributes([], '');
    }

    /**
     * @test
     */
    public function test_generateAttributes_basic(): void
    {
        $this->assertGenerateAttributes(
            ['href' => 'dyn?a=foo&b=bar'],
            'href="dyn?a=foo&amp;b=bar"'
        );
    }

    /**
     * @test
     */
    public function test_generateAttributes_doubleQuote(): void
    {
        $this->assertGenerateAttributes(
            ['style' => 'font-family:"Courier New";'],
            'style="font-family:&quot;Courier New&quot;;"'
        );
    }

    /**
     * @test
     */
    public function test_generateAttributes_singleQuote(): void
    {
        $this->assertGenerateAttributes(
            ['style' => 'font-family:\'Courier New\';'],
            'style="font-family:\'Courier New\';"'
        );
    }

    /**
     * @test
     */
    public function test_generateAttributes_multiple(): void
    {
        $this->assertGenerateAttributes(
            ['src' => 'picture.jpg', 'alt' => 'Short & interesting'],
            'src="picture.jpg" alt="Short &amp; interesting"'
        );
    }

    /**
     * @test
     */
    public function test_generateAttributes_specialChar(): void
    {
        $theta_char = $this->_entity_lookup->table['theta'];
        $this->assertGenerateAttributes(
            ['title' => 'Theta is ' . $theta_char],
            'title="Theta is ' . $theta_char . '"'
        );
    }

    /**
     * @test
     */
    public function test_generateAttributes_minimized(): void
    {
        $this->config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $this->assertGenerateAttributes(
            ['compact' => 'compact'], 'compact', 'menu'
        );
    }

    /**
     * @test
     */
    public function test_generateFromTokens(): void
    {
        $this->assertGeneration(
            [
                new Start('b'),
                new Text('Foobar!'),
                new End('b')
            ],
            '<b>Foobar!</b>'
        );

    }

    /**
     * @param $tokens
     * @param $expect
     *
     * @throws \HTMLPurifier\Exception
     */
    private function assertGeneration($tokens, $expect): void
    {
        $generator = new Generator($this->config, $this->context);
        $result = $generator->generateFromTokens($tokens);

        static::assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function test_generateFromTokens_Scripting(): void
    {
        $this->assertGeneration(
            [
                new Start('script'),
                new Text('alert(3 < 5);'),
                new End('script')
            ],
            "<script><!--//--><![CDATA[//><!--\nalert(3 < 5);\n//--><!]]></script>"
        );
    }

    /**
     * @test
     */
    public function test_generateFromTokens_Scripting_missingCloseTag(): void
    {
        $this->assertGeneration(
            [
                new Start('script'),
                new Text('alert(3 < 5);'),
            ],
            "<script>alert(3 &lt; 5);"
        );
    }

    /**
     * @test
     */
    public function test_generateFromTokens_Scripting_doubleBlock(): void
    {
        $this->assertGeneration(
            [
                new Start('script'),
                new Text('alert(3 < 5);'),
                new Text('foo();'),
                new End('script')
            ],
            "<script>alert(3 &lt; 5);foo();</script>"
        );
    }

    /**
     * @test
     */
    public function test_generateFromTokens_Scripting_disableWrapper(): void
    {
        $this->config->set('Output.CommentScriptContents', false);
        $this->assertGeneration(
            [
                new Start('script'),
                new Text('alert(3 < 5);'),
                new End('script')
            ],
            "<script>alert(3 &lt; 5);</script>"
        );
    }

    /**
     * @test
     */
    public function test_generateFromTokens_XHTMLoff(): void
    {
        $this->config->set('HTML.XHTML', false);

        // omit trailing slash
        $this->assertGeneration(
            [new EmptyToken('br')],
            '<br>'
        );

        // there should be a test for attribute minimization, but it is
        // impossible for something like that to happen due to our current
        // definitions! fix it later

        // namespaced attributes must be dropped
        $this->assertGeneration(
            [new Start('p', ['xml:lang'=>'fr'])],
            '<p>'
        );

    }

    /**
     * @test
     */
    public function test_generateFromTokens_TidyFormat(): void
    {
        // abort test if tidy isn't loaded
        if (!\extension_loaded('tidy')) {
            $this->markTestSkipped('tidy is not loaded');
        }

        // just don't test; Tidy is exploding on me.
        $this->markTestSkipped('just don\'t test; Tidy is exploding on me.');
        return;

        $this->config->set('Core.TidyFormat', true);
        $this->config->set('Output.Newline', "\n");

        // nice wrapping please
        $this->assertGeneration(
            [
                new Start('div'),
                new Text('Text'),
                new End('div')
            ],
            "<div>\n  Text\n</div>\n"
        );

    }

    /**
     * @test
     */
    public function test_generateFromTokens_sortAttr(): void
    {
        $this->config->set('Output.SortAttr', true);

        $this->assertGeneration(
            [new Start('p', ['b'=>'c', 'a'=>'d'])],
            '<p a="d" b="c">'
        );
    }
}
