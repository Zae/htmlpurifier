<?php

use HTMLPurifier\Lexer\DOMLex;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Comment;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\Text;
use HTMLPurifier\Token\Start;

class HTMLPurifier_LexerTest extends HTMLPurifier_Harness
{

    protected $_has_pear = false;

    public function __construct()
    {
        parent::__construct();
        if ($GLOBALS['HTMLPurifierTest']['PH5P']) {
            require_once 'HTMLPurifier/Lexer/PH5P.php';
        }
    }

    // HTMLPurifier_Lexer::create() --------------------------------------------

    public function test_create()
    {
        $this->config->set('Core.MaintainLineNumbers', true);
        $lexer = HTMLPurifier_Lexer::create($this->config);
        $this->assertIsA($lexer, 'HTMLPurifier_Lexer_DirectLex');
    }

    public function test_create_objectLexerImpl()
    {
        $this->config->set('Core.LexerImpl', new HTMLPurifier_Lexer_DirectLex());
        $lexer = HTMLPurifier_Lexer::create($this->config);
        $this->assertIsA($lexer, 'HTMLPurifier_Lexer_DirectLex');
    }

    public function test_create_unknownLexer()
    {
        $this->config->set('Core.LexerImpl', 'AsdfAsdf');
        $this->expectException(new HTMLPurifier_Exception('Cannot instantiate unrecognized Lexer type AsdfAsdf'));
        HTMLPurifier_Lexer::create($this->config);
    }

    public function test_create_incompatibleLexer()
    {
        $this->config->set('Core.LexerImpl', 'DOMLex');
        $this->config->set('Core.MaintainLineNumbers', true);
        $this->expectException(new HTMLPurifier_Exception('Cannot use lexer that does not support line numbers with Core.MaintainLineNumbers or Core.CollectErrors (use DirectLex instead)'));
        HTMLPurifier_Lexer::create($this->config);
    }

    // HTMLPurifier_Lexer->parseData() -----------------------------------------

    public function assertParseData($input, $expect = true, $is_attr = false)
    {
        if ($expect === true) $expect = $input;
        $lexer = new HTMLPurifier_Lexer();
        $this->assertIdentical($expect, $lexer->parseData($input, $is_attr, $this->config));
    }

    public function test_parseData_plainText()
    {
        $this->assertParseData('asdf');
    }

    public function test_parseData_ampersandEntity()
    {
        $this->assertParseData('&amp;', '&');
    }

    public function test_parseData_quotEntity()
    {
        $this->assertParseData('&quot;', '"');
    }

    public function test_parseData_aposNumericEntity()
    {
        $this->assertParseData('&#039;', "'");
    }

    public function test_parseData_aposCompactNumericEntity()
    {
        $this->assertParseData('&#39;', "'");
    }

    public function test_parseData_adjacentAmpersandEntities()
    {
        $this->assertParseData('&amp;&amp;&amp;', '&&&');
    }

    public function test_parseData_trailingUnescapedAmpersand()
    {
        $this->assertParseData('&amp;&', '&&');
    }

    public function test_parseData_internalUnescapedAmpersand()
    {
        $this->assertParseData('Procter & Gamble');
    }

    public function test_parseData_improperEntityFaultToleranceTest()
    {
        $this->assertParseData('&#x2D;', '-');
    }

    public function test_parseData_noTrailingSemi()
    {
        $this->assertParseData('&ampA', '&A');
    }

    public function test_parseData_noTrailingSemiAttr()
    {
        $this->assertParseData('&ampA', '&ampA', true);
    }

    public function test_parseData_T119()
    {
        $this->assertParseData('&ampA', '&ampA', true);
    }

    public function test_parseData_T119b()
    {
        $this->assertParseData('&trade=', true, true);
    }

    public function test_parseData_legacy1()
    {
        $this->config->set('Core.LegacyEntityDecoder', true);
        $this->assertParseData('&ampa', true);
        $this->assertParseData('&amp=', "&=");
        $this->assertParseData('&ampa', true, true);
        $this->assertParseData('&amp=', "&=", true);
        $this->assertParseData('&lta', true);
        $this->assertParseData('&lt=', "<=");
        $this->assertParseData('&lta', true, true);
        $this->assertParseData('&lt=', "<=", true);
    }

    public function test_parseData_nonlegacy1()
    {
        $this->assertParseData('&ampa', "&a");
        $this->assertParseData('&amp=', "&=");
        $this->assertParseData('&ampa', true, true);
        $this->assertParseData('&amp=', true, true);
        $this->assertParseData('&lta', "<a");
        $this->assertParseData('&lt=', "<=");
        $this->assertParseData('&lta', true, true);
        $this->assertParseData('&lt=', true, true);
        $this->assertParseData('&lta;', "<a;");
    }

    public function test_parseData_noTrailingSemiNever()
    {
        $this->assertParseData('&imath');
    }

    // HTMLPurifier_Lexer->extractBody() ---------------------------------------

    public function assertExtractBody($text, $extract = true)
    {
        $lexer = new HTMLPurifier_Lexer();
        $result = $lexer->extractBody($text);
        if ($extract === true) $extract = $text;
        $this->assertIdentical($extract, $result);
    }

    public function test_extractBody_noBodyTags()
    {
        $this->assertExtractBody('<b>Bold</b>');
    }

    public function test_extractBody_lowercaseBodyTags()
    {
        $this->assertExtractBody('<html><body><b>Bold</b></body></html>', '<b>Bold</b>');
    }

    public function test_extractBody_uppercaseBodyTags()
    {
        $this->assertExtractBody('<HTML><BODY><B>Bold</B></BODY></HTML>', '<B>Bold</B>');
    }

    public function test_extractBody_realisticUseCase()
    {
        $this->assertExtractBody(
'<?xml version="1.0"
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
      <title>xyz</title>
   </head>
   <body>
      <form method="post" action="whatever1">
         <div>
            <input type="text" name="username" />
            <input type="text" name="password" />
            <input type="submit" />
         </div>
      </form>
   </body>
</html>',
    '
      <form method="post" action="whatever1">
         <div>
            <input type="text" name="username" />
            <input type="text" name="password" />
            <input type="submit" />
         </div>
      </form>
   ');
    }

    public function test_extractBody_bodyWithAttributes()
    {
        $this->assertExtractBody('<html><body bgcolor="#F00"><b>Bold</b></body></html>', '<b>Bold</b>');
    }

    public function test_extractBody_preserveUnclosedBody()
    {
        $this->assertExtractBody('<body>asdf'); // not closed, don't accept
    }

    public function test_extractBody_useLastBody()
    {
        $this->assertExtractBody('<body>foo</body>bar</body>', 'foo</body>bar');
    }

    public function test_extractBody_ignoreCommented()
    {
        $this->assertExtractBody('$<!-- <body>foo</body> -->^');
    }

    public function test_extractBody_butCanStillWork()
    {
        $this->assertExtractBody('<!-- b --><body>a</body>', 'a');
    }

    // HTMLPurifier_Lexer->tokenizeHTML() --------------------------------------

    public function assertTokenization($input, $expect, $alt_expect = array())
    {
        $lexers = array();
        $lexers['DirectLex']  = new HTMLPurifier_Lexer_DirectLex();
        if (class_exists('DOMDocument')) {
            $lexers['DOMLex'] = new DOMLex();
            $lexers['PH5P']   = new _PH5P();
        }
        foreach ($lexers as $name => $lexer) {
            $result = $lexer->tokenizeHTML($input, $this->config, $this->context);
            if (isset($alt_expect[$name])) {
                if ($alt_expect[$name] === false) continue;
                $t_expect = $alt_expect[$name];
                $this->assertIdentical($result, $alt_expect[$name], "$name: %s");
            } else {
                $t_expect = $expect;
                $this->assertIdentical($result, $expect, "$name: %s");
            }
            if ($t_expect != $result) {
                printTokens($result);
            }
        }
    }

    public function test_tokenizeHTML_emptyInput()
    {
        $this->assertTokenization('', array());
    }

    public function test_tokenizeHTML_plainText()
    {
        $this->assertTokenization(
            'This is regular text.',
            array(
                new Text('This is regular text.')
            )
        );
    }

    public function test_tokenizeHTML_textAndTags()
    {
        $this->assertTokenization(
            'This is <b>bold</b> text',
            array(
                new Text('This is '),
                new Start('b', array()),
                new Text('bold'),
                new End('b'),
                new Text(' text'),
            )
        );
    }

    public function test_tokenizeHTML_normalizeCase()
    {
        $this->assertTokenization(
            '<DIV>Totally rad dude. <b>asdf</b></div>',
            array(
                new Start('DIV', array()),
                new Text('Totally rad dude. '),
                new Start('b', array()),
                new Text('asdf'),
                new End('b'),
                new End('div'),
            )
        );
    }

    public function test_tokenizeHTML_notWellFormed()
    {
        $this->assertTokenization(
            '<asdf></asdf><d></d><poOloka><poolasdf><ds></asdf></ASDF>',
            array(
                new Start('asdf'),
                new End('asdf'),
                new Start('d'),
                new End('d'),
                new Start('poOloka'),
                new Start('poolasdf'),
                new Start('ds'),
                new End('asdf'),
                new End('ASDF'),
            ),
            array(
                'DOMLex' => $alt = array(
                    new EmptyToken('asdf'),
                    new EmptyToken('d'),
                    new Start('pooloka'),
                    new Start('poolasdf'),
                    new EmptyToken('ds'),
                    new End('poolasdf'),
                    new End('pooloka'),
                ),
                // 20140831: Weird, but whatever...
                'PH5P' => array(new EmptyToken('asdf')),
            )
        );
    }

    public function test_tokenizeHTML_whitespaceInTag()
    {
        $this->assertTokenization(
            '<a'."\t".'href="foobar.php"'."\n".'title="foo!">Link to <b id="asdf">foobar</b></a>',
            array(
                new Start('a',array('href'=>'foobar.php','title'=>'foo!')),
                new Text('Link to '),
                new Start('b',array('id'=>'asdf')),
                new Text('foobar'),
                new End('b'),
                new End('a'),
            )
        );
    }

    public function test_tokenizeHTML_singleAttribute()
    {
        $this->assertTokenization(
            '<br style="&amp;" />',
            array(
                new EmptyToken('br', array('style' => '&'))
            )
        );
    }

    public function test_tokenizeHTML_emptyTag()
    {
        $this->assertTokenization(
            '<br />',
            array( new EmptyToken('br') )
        );
    }

    public function test_tokenizeHTML_comment()
    {
        $this->assertTokenization(
            '<!-- Comment -->',
            array( new Comment(' Comment ') )
        );
    }

    public function test_tokenizeHTML_malformedComment()
    {
        $this->assertTokenization(
            '<!-- not so well formed --->',
            array( new Comment(' not so well formed -') )
        );
    }

    public function test_tokenizeHTML_unterminatedTag()
    {
        $this->assertTokenization(
            '<a href=""',
            array( new Text('<a href=""') ),
            array(
                // I like our behavior better, but it's non-standard
                'DOMLex'   => array( new EmptyToken('a', array('href'=>'')) ),
                'PH5P' => false, // total barfing, grabs scaffolding too
            )
        );
    }

    public function test_tokenizeHTML_specialEntities()
    {
        $this->assertTokenization(
            '&lt;b&gt;',
            array(
                new Text('<b>')
            ),
            array(
                // some parsers will separate entities out
                'PH5P' => array(
                    new Text('<'),
                    new Text('b'),
                    new Text('>'),
                ),
            )
        );
    }

    public function test_tokenizeHTML_earlyQuote()
    {
        $this->assertTokenization(
            '<a "=>',
            array( new EmptyToken('a') ),
            array(
                // we barf on this input
                'DirectLex' => array(
                    new Start('a', array('"' => ''))
                ),
                'PH5P' => false, // behavior varies; handle this personally
            )
        );
    }

    public function test_tokenizeHTML_earlyQuote_PH5P()
    {
        if (!class_exists('DOMDocument')) return;
        $lexer = new _PH5P();
        $result = $lexer->tokenizeHTML('<a "=>', $this->config, $this->context);
        if ($this->context->get('PH5PError', true)) {
            $this->assertIdentical(array(
                new Start('a', array('"' => ''))
            ), $result);
        } else {
            $this->assertIdentical(array(
                new EmptyToken('a', array('"' => ''))
            ), $result);
        }
    }

    public function test_tokenizeHTML_unescapedQuote()
    {
        $this->assertTokenization(
            '"',
            array( new Text('"') )
        );
    }

    public function test_tokenizeHTML_escapedQuote()
    {
        $this->assertTokenization(
            '&quot;',
            array( new Text('"') )
        );
    }

    public function test_tokenizeHTML_cdata()
    {
        $this->assertTokenization(
            '<![CDATA[You <b>can&#39;t</b> get me!]]>',
            array( new Text('You <b>can&#39;t</b> get me!') ),
            array(
                'PH5P' =>  array(
                    new Text('You '),
                    new Text('<'),
                    new Text('b'),
                    new Text('>'),
                    new Text('can'),
                    new Text('&'),
                    new Text('#39;t'),
                    new Text('<'),
                    new Text('/b'),
                    new Text('>'),
                    new Text(' get me!'),
                ),
            )
        );
    }

    public function test_tokenizeHTML_characterEntity()
    {
        $this->assertTokenization(
            '&theta;',
            array( new Text("\xCE\xB8") )
        );
    }

    public function test_tokenizeHTML_characterEntityInCDATA()
    {
        $this->assertTokenization(
            '<![CDATA[&rarr;]]>',
            array( new Text("&rarr;") ),
            array(
                'PH5P' => array(
                    new Text('&'),
                    new Text('rarr;'),
                ),
            )
        );
    }

    public function test_tokenizeHTML_entityInAttribute()
    {
        $this->assertTokenization(
            '<a href="index.php?title=foo&amp;id=bar">Link</a>',
            array(
                new Start('a',array('href' => 'index.php?title=foo&id=bar')),
                new Text('Link'),
                new End('a'),
            )
        );
    }

    public function test_tokenizeHTML_preserveUTF8()
    {
        $this->assertTokenization(
            "\xCE\xB8",
            array( new Text("\xCE\xB8") )
        );
    }

    public function test_tokenizeHTML_specialEntityInAttribute()
    {
        $this->assertTokenization(
            '<br test="x &lt; 6" />',
            array( new EmptyToken('br', array('test' => 'x < 6')) )
        );
    }

    public function test_tokenizeHTML_emoticonProtection()
    {
        $this->assertTokenization(
            '<b>Whoa! <3 That\'s not good >.></b>',
            array(
                new Start('b'),
                new Text('Whoa! '),
                new Text('<'),
                new Text('3 That\'s not good >.>'),
                new End('b')
            ),
            array(
                // text is absorbed together
                'DOMLex' => array(
                    new Start('b'),
                    new Text('Whoa! <3 That\'s not good >.>'),
                    new End('b'),
                ),
                'PH5P' => array( // interesting grouping
                    new Start('b'),
                    new Text('Whoa! '),
                    new Text('<'),
                    new Text('3 That\'s not good >.>'),
                    new End('b'),
                ),
            )
        );
    }

    public function test_tokenizeHTML_commentWithFunkyChars()
    {
        $this->assertTokenization(
            '<!-- This >< comment --><br />',
            array(
                new Comment(' This >< comment '),
                new EmptyToken('br'),
            )
        );
    }

    public function test_tokenizeHTML_unterminatedComment()
    {
        $this->assertTokenization(
            '<!-- This >< comment',
            array( new Comment(' This >< comment') ),
            array(
                'DOMLex'   => false,
                'PH5P'     => false,
            )
        );
    }

    public function test_tokenizeHTML_scriptCDATAContents()
    {
        $this->config->set('HTML.Trusted', true);
        $this->assertTokenization(
            'Foo: <script>alert("<foo>");</script>',
            array(
                new Text('Foo: '),
                new Start('script'),
                new Text('alert("<foo>");'),
                new End('script'),
            ),
            array(
                // PH5P, for some reason, bubbles the script to <head>
                'PH5P' => false,
            )
        );
    }

    public function test_tokenizeHTML_entitiesInComment()
    {
        $this->assertTokenization(
            '<!-- This comment < &lt; & -->',
            array( new Comment(' This comment < &lt; & ') )
        );
    }

    public function test_tokenizeHTML_attributeWithSpecialCharacters()
    {
        $this->assertTokenization(
            '<a href="><>">',
            array( new EmptyToken('a', array('href' => '><>')) ),
            array(
                'DirectLex' => array(
                    new Start('a', array('href' => '')),
                    new Text('<'),
                    new Text('">'),
                )
            )
        );
    }

    public function test_tokenizeHTML_emptyTagWithSlashInAttribute()
    {
        $this->assertTokenization(
            '<param name="src" value="http://example.com/video.wmv" />',
            array( new EmptyToken('param', array('name' => 'src', 'value' => 'http://example.com/video.wmv')) )
        );
    }

    public function test_tokenizeHTML_style()
    {
        $extra = array(
                // PH5P doesn't seem to like style tags
                'PH5P' => false,
                // DirectLex defers to RemoveForeignElements for textification
                'DirectLex' => array(
                    new Start('style', array('type' => 'text/css')),
                    new Comment("\ndiv {}\n"),
                    new End('style'),
                ),
            );
        if (!defined('LIBXML_VERSION')) {
            // LIBXML_VERSION is missing in early versions of PHP
            // prior to 1.30 of php-src/ext/libxml/libxml.c (version-wise,
            // this translates to 5.0.x. In such cases, punt the test entirely.
            return;
        } elseif (LIBXML_VERSION < 20628) {
            // libxml's behavior is wrong prior to this version, so make
            // appropriate accomodations
            $extra['DOMLex'] = $extra['DirectLex'];
        }
        $this->assertTokenization(
'<style type="text/css"><!--
div {}
--></style>',
            array(
                new Start('style', array('type' => 'text/css')),
                new Text("\ndiv {}\n"),
                new End('style'),
            ),
            $extra
        );
    }

    public function test_tokenizeHTML_tagWithAtSignAndExtraGt()
    {
        $alt_expect = array(
            // Technically this is invalid, but it won't be a
            // problem with invalid element removal; also, this
            // mimics Mozilla's parsing of the tag.
            new Start('a@'),
            new Text('>'),
        );
        $this->assertTokenization(
            '<a@>>',
            array(
                new Start('a'),
                new Text('>'),
                new End('a'),
            ),
            array(
                'DirectLex' => $alt_expect,
            )
        );
    }

    public function test_tokenizeHTML_emoticonHeart()
    {
        $this->assertTokenization(
            '<br /><3<br />',
            array(
                new EmptyToken('br'),
                new Text('<'),
                new Text('3'),
                new EmptyToken('br'),
            ),
            array(
                'DOMLex' => array(
                    new EmptyToken('br'),
                    new Text('<3'),
                    new EmptyToken('br'),
                ),
            )
        );
    }

    public function test_tokenizeHTML_emoticonShiftyEyes()
    {
        $this->assertTokenization(
            '<b><<</b>',
            array(
                new Start('b'),
                new Text('<'),
                new Text('<'),
                new End('b'),
            ),
            array(
                'DOMLex' => array(
                    new Start('b'),
                    new Text('<<'),
                    new End('b'),
                ),
            )
        );
    }

    public function test_tokenizeHTML_eon1996()
    {
        $this->assertTokenization(
            '< <b>test</b>',
            array(
                new Text('<'),
                new Text(' '),
                new Start('b'),
                new Text('test'),
                new End('b'),
            ),
            array(
                'DOMLex' => array(
                    new Text('< '),
                    new Start('b'),
                    new Text('test'),
                    new End('b'),
                ),
            )
        );
    }

    public function test_tokenizeHTML_bodyInCDATA()
    {
        $alt_tokens = array(
            new Text('<'),
            new Text('body'),
            new Text('>'),
            new Text('Foo'),
            new Text('<'),
            new Text('/body'),
            new Text('>'),
        );
        $this->assertTokenization(
            '<![CDATA[<body>Foo</body>]]>',
            array(
                new Text('<body>Foo</body>'),
            ),
            array(
                'PH5P' => $alt_tokens,
            )
        );
    }

    public function test_tokenizeHTML_()
    {
        $this->assertTokenization(
            '<a><img /></a>',
            array(
                new Start('a'),
                new EmptyToken('img'),
                new End('a'),
            )
        );
    }

    public function test_tokenizeHTML_ignoreIECondComment()
    {
        $this->assertTokenization(
            '<!--[if IE]>foo<a>bar<!-- baz --><![endif]-->',
            array()
        );
    }

    public function test_tokenizeHTML_removeProcessingInstruction()
    {
        $this->config->set('Core.RemoveProcessingInstructions', true);
        $this->assertTokenization(
            '<?xml blah blah ?>',
            array()
        );
    }

   public function test_tokenizeHTML_removeNewline()
   {
        $this->config->set('Core.NormalizeNewlines', true);
        $this->assertTokenization(
            "plain\rtext\r\n",
            array(
                new Text("plain\ntext\n")
            )
        );
   }

   public function test_tokenizeHTML_noRemoveNewline()
   {
        $this->config->set('Core.NormalizeNewlines', false);
        $this->assertTokenization(
            "plain\rtext\r\n",
            array(
                new Text("plain\rtext\r\n")
            )
        );
     }

    public function test_tokenizeHTML_conditionalCommentUngreedy()
    {
        $this->assertTokenization(
            '<!--[if gte mso 9]>a<![endif]-->b<!--[if gte mso 9]>c<![endif]-->',
            array(
                new Text("b")
            )
        );
    }

    public function test_tokenizeHTML_imgTag()
    {
        $start = array(
                        new Start('img',
                            array(
                                'src' => 'img_11775.jpg',
                                'alt' => '[Img #11775]',
                                'id' => 'EMBEDDED_IMG_11775',
                            )
                        )
                    );
        $this->assertTokenization(
            '<img src="img_11775.jpg" alt="[Img #11775]" id="EMBEDDED_IMG_11775" >',
            array(
                new EmptyToken('img',
                    array(
                        'src' => 'img_11775.jpg',
                        'alt' => '[Img #11775]',
                        'id' => 'EMBEDDED_IMG_11775',
                    )
                )
            ),
            array(
                'DirectLex' => $start,
                )
        );
    }

    public function test_tokenizeHTML_prematureDivClose()
    {
        $this->assertTokenization(
            '</div>dont<b>die</b>',
            array(
                new End('div'),
                new Text('dont'),
                new Start('b'),
                new Text('die'),
                new End('b'),
            ),
            array(
                'DOMLex' => $alt = array(
                    new Text('dont'),
                    new Start('b'),
                    new Text('die'),
                    new End('b')
                ),
                'PH5P' => $alt
            )
        );
    }


    /*

    public function test_tokenizeHTML_()
    {
        $this->assertTokenization(
            ,
            array(

            )
        );
    }
    */

}

// vim: et sw=4 sts=4
