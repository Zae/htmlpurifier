<?php

use HTMLPurifier\EntityLookup;
use HTMLPurifier\Generator;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\Text;
use HTMLPurifier\Token\Start;

class HTMLPurifier_GeneratorTest extends HTMLPurifier_Harness
{

    /**
     * Entity lookup table to help for a few tests.
     */
    private $_entity_lookup;

    public function __construct()
    {
        parent::__construct();
        $this->_entity_lookup = EntityLookup::instance();
    }

    public function setUp()
    {
        parent::setUp();
        $this->config->set('Output.Newline', "\n");
    }

    /**
     * Creates a generator based on config and context member variables.
     */
    protected function createGenerator()
    {
        return new Generator($this->config, $this->context);
    }

    protected function assertGenerateFromToken($token, $html)
    {
        $generator = $this->createGenerator();
        $result = $generator->generateFromToken($token);
        $this->assertIdentical($result, $html);
    }

    public function test_generateFromToken_text()
    {
        $this->assertGenerateFromToken(
            new Text('Foobar.<>'),
            'Foobar.&lt;&gt;'
        );
    }

    public function test_generateFromToken_startWithAttr()
    {
        $this->assertGenerateFromToken(
            new Start('a',
                array('href' => 'dyn?a=foo&b=bar')
            ),
            '<a href="dyn?a=foo&amp;b=bar">'
        );
    }

    public function test_generateFromToken_end()
    {
        $this->assertGenerateFromToken(
            new End('b'),
            '</b>'
        );
    }

    public function test_generateFromToken_emptyWithAttr()
    {
        $this->assertGenerateFromToken(
            new EmptyToken('br',
                array('style' => 'font-family:"Courier New";')
            ),
            '<br style="font-family:&quot;Courier New&quot;;" />'
        );
    }

    public function test_generateFromToken_startNoAttr()
    {
        $this->assertGenerateFromToken(
            new Start('asdf'),
            '<asdf>'
        );
    }

    public function test_generateFromToken_emptyNoAttr()
    {
        $this->assertGenerateFromToken(
            new EmptyToken('br'),
            '<br />'
        );
    }

    public function test_generateFromToken_error()
    {
        $this->expectError('Cannot generate HTML from non-HTMLPurifier\HTMLPurifier_Token object');
        $this->assertGenerateFromToken( null, '' );
    }

    public function test_generateFromToken_unicode()
    {
        $theta_char = $this->_entity_lookup->table['theta'];
        $this->assertGenerateFromToken(
            new Text($theta_char),
            $theta_char
        );
    }

    public function test_generateFromToken_backtick()
    {
        $this->assertGenerateFromToken(
            new Start('img', array('alt' => '`foo')),
            '<img alt="`foo ">'
        );
    }

    public function test_generateFromToken_backtickDisabled()
    {
        $this->config->set('Output.FixInnerHTML', false);
        $this->assertGenerateFromToken(
            new Start('img', array('alt' => '`')),
            '<img alt="`">'
        );
    }

    public function test_generateFromToken_backtickNoChange()
    {
        $this->assertGenerateFromToken(
            new Start('img', array('alt' => '`foo` bar')),
            '<img alt="`foo` bar">'
        );
    }

    public function assertGenerateAttributes($attr, $expect, $element = false)
    {
        $generator = $this->createGenerator();
        $result = $generator->generateAttributes($attr, $element);
        $this->assertIdentical($result, $expect);
    }

    public function test_generateAttributes_blank()
    {
        $this->assertGenerateAttributes(array(), '');
    }

    public function test_generateAttributes_basic()
    {
        $this->assertGenerateAttributes(
            array('href' => 'dyn?a=foo&b=bar'),
            'href="dyn?a=foo&amp;b=bar"'
        );
    }

    public function test_generateAttributes_doubleQuote()
    {
        $this->assertGenerateAttributes(
            array('style' => 'font-family:"Courier New";'),
            'style="font-family:&quot;Courier New&quot;;"'
        );
    }

    public function test_generateAttributes_singleQuote()
    {
        $this->assertGenerateAttributes(
            array('style' => 'font-family:\'Courier New\';'),
            'style="font-family:\'Courier New\';"'
        );
    }

    public function test_generateAttributes_multiple()
    {
        $this->assertGenerateAttributes(
            array('src' => 'picture.jpg', 'alt' => 'Short & interesting'),
            'src="picture.jpg" alt="Short &amp; interesting"'
        );
    }

    public function test_generateAttributes_specialChar()
    {
        $theta_char = $this->_entity_lookup->table['theta'];
        $this->assertGenerateAttributes(
            array('title' => 'Theta is ' . $theta_char),
            'title="Theta is ' . $theta_char . '"'
        );
    }


    public function test_generateAttributes_minimized()
    {
        $this->config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $this->assertGenerateAttributes(
            array('compact' => 'compact'), 'compact', 'menu'
        );
    }

    public function test_generateFromTokens()
    {
        $this->assertGeneration(
            array(
                new Start('b'),
                new Text('Foobar!'),
                new End('b')
            ),
            '<b>Foobar!</b>'
        );

    }

    protected function assertGeneration($tokens, $expect)
    {
        $generator = new Generator($this->config, $this->context);
        $result = $generator->generateFromTokens($tokens);
        $this->assertIdentical($expect, $result);
    }

    public function test_generateFromTokens_Scripting()
    {
        $this->assertGeneration(
            array(
                new Start('script'),
                new Text('alert(3 < 5);'),
                new End('script')
            ),
            "<script><!--//--><![CDATA[//><!--\nalert(3 < 5);\n//--><!]]></script>"
        );
    }

    public function test_generateFromTokens_Scripting_missingCloseTag()
    {
        $this->assertGeneration(
            array(
                new Start('script'),
                new Text('alert(3 < 5);'),
            ),
            "<script>alert(3 &lt; 5);"
        );
    }

    public function test_generateFromTokens_Scripting_doubleBlock()
    {
        $this->assertGeneration(
            array(
                new Start('script'),
                new Text('alert(3 < 5);'),
                new Text('foo();'),
                new End('script')
            ),
            "<script>alert(3 &lt; 5);foo();</script>"
        );
    }

    public function test_generateFromTokens_Scripting_disableWrapper()
    {
        $this->config->set('Output.CommentScriptContents', false);
        $this->assertGeneration(
            array(
                new Start('script'),
                new Text('alert(3 < 5);'),
                new End('script')
            ),
            "<script>alert(3 &lt; 5);</script>"
        );
    }

    public function test_generateFromTokens_XHTMLoff()
    {
        $this->config->set('HTML.XHTML', false);

        // omit trailing slash
        $this->assertGeneration(
            array( new EmptyToken('br') ),
            '<br>'
        );

        // there should be a test for attribute minimization, but it is
        // impossible for something like that to happen due to our current
        // definitions! fix it later

        // namespaced attributes must be dropped
        $this->assertGeneration(
            array( new Start('p', array('xml:lang'=>'fr')) ),
            '<p>'
        );

    }

    public function test_generateFromTokens_TidyFormat()
    {
        // abort test if tidy isn't loaded
        if (!extension_loaded('tidy')) return;

        // just don't test; Tidy is exploding on me.
        return;

        $this->config->set('Core.TidyFormat', true);
        $this->config->set('Output.Newline', "\n");

        // nice wrapping please
        $this->assertGeneration(
            array(
                new Start('div'),
                new Text('Text'),
                new End('div')
            ),
            "<div>\n  Text\n</div>\n"
        );

    }

    public function test_generateFromTokens_sortAttr()
    {
        $this->config->set('Output.SortAttr', true);

        $this->assertGeneration(
            array( new Start('p', array('b'=>'c', 'a'=>'d')) ),
            '<p a="d" b="c">'
        );

    }

}

// vim: et sw=4 sts=4
