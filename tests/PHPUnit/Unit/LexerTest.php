<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit;

use _PH5P;
use HTMLPurifier\Exception;
use HTMLPurifier\Lexer;
use HTMLPurifier\Lexer\DirectLex;
use HTMLPurifier\Lexer\DOMLex;
use HTMLPurifier\Token\Comment;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Token\Text;

/**
 * Class LexerTest
 *
 * @package HTMLPurifier\Tests\Unit
 */
class LexerTest extends TestCase
{
    // HTMLPurifier_Lexer::create() --------------------------------------------

    protected function setUp(): void
    {
        if (!class_exists(_PH5P::class)) {
            static::markTestSkipped('_PHP5 class not loaded.');
        }

        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /**
     * @test
     */
    public function test_create(): void
    {
        $this->config->set('Core.MaintainLineNumbers', true);
        $lexer = Lexer::create($this->config);

        static::assertInstanceOf(DirectLex::class, $lexer);
    }

    /**
     * @test
     */
    public function test_create_objectLexerImpl(): void
    {
        $this->config->set('Core.LexerImpl', new DirectLex());
        $lexer = Lexer::create($this->config);

        static::assertInstanceOf(DirectLex::class, $lexer);
    }

    /**
     * @test
     */
    public function test_create_unknownLexer(): void
    {
        $this->config->set('Core.LexerImpl', 'AsdfAsdf');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot instantiate unrecognized Lexer type AsdfAsdf');

        Lexer::create($this->config);
    }

    /**
     * @test
     */
    public function test_create_incompatibleLexer(): void
    {
        $this->config->set('Core.LexerImpl', 'DOMLex');
        $this->config->set('Core.MaintainLineNumbers', true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot use lexer that does not support line numbers with Core.MaintainLineNumbers or Core.CollectErrors (use DirectLex instead)');

        Lexer::create($this->config);
    }

    // HTMLPurifier_Lexer->parseData() -----------------------------------------

    /**
     * @param string        $input
     * @param bool|string   $expect
     * @param bool          $is_attr
     *
     * @throws Exception
     */
    private function assertParseData($input, $expect = true, bool $is_attr = false): void
    {
        if ($expect === true) {
            $expect = $input;
        }

        $lexer = new Lexer();
        static::assertEquals($expect, $lexer->parseData($input, $is_attr, $this->config));
    }

    /**
     * @test
     */
    public function test_parseData_plainText(): void
    {
        $this->assertParseData('asdf');
    }

    /**
     * @test
     */
    public function test_parseData_ampersandEntity(): void
    {
        $this->assertParseData('&amp;', '&');
    }

    /**
     * @test
     */
    public function test_parseData_quotEntity(): void
    {
        $this->assertParseData('&quot;', '"');
    }

    /**
     * @test
     */
    public function test_parseData_aposNumericEntity(): void
    {
        $this->assertParseData('&#039;', "'");
    }

    /**
     * @test
     */
    public function test_parseData_aposCompactNumericEntity(): void
    {
        $this->assertParseData('&#39;', "'");
    }

    /**
     * @test
     */
    public function test_parseData_adjacentAmpersandEntities(): void
    {
        $this->assertParseData('&amp;&amp;&amp;', '&&&');
    }

    /**
     * @test
     */
    public function test_parseData_trailingUnescapedAmpersand(): void
    {
        $this->assertParseData('&amp;&', '&&');
    }

    /**
     * @test
     */
    public function test_parseData_internalUnescapedAmpersand(): void
    {
        $this->assertParseData('Procter & Gamble');
    }

    /**
     * @test
     */
    public function test_parseData_improperEntityFaultToleranceTest(): void
    {
        $this->assertParseData('&#x2D;', '-');
    }

    /**
     * @test
     */
    public function test_parseData_noTrailingSemi(): void
    {
        $this->assertParseData('&ampA', '&A');
    }

    /**
     * @test
     */
    public function test_parseData_noTrailingSemiAttr(): void
    {
        $this->assertParseData('&ampA', '&ampA', true);
    }

    /**
     * @test
     */
    public function test_parseData_T119(): void
    {
        $this->assertParseData('&ampA', '&ampA', true);
    }

    /**
     * @test
     */
    public function test_parseData_T119b(): void
    {
        $this->assertParseData('&trade=', true, true);
    }

    /**
     * @test
     */
    public function test_parseData_legacy1(): void
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

    /**
     * @test
     */
    public function test_parseData_nonlegacy1(): void
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

    /**
     * @test
     */
    public function test_parseData_noTrailingSemiNever(): void
    {
        $this->assertParseData('&imath');
    }

    // HTMLPurifier_Lexer->extractBody() ---------------------------------------

    /**
     * @param      $text
     * @param bool $extract
     */
    public function assertExtractBody($text, $extract = true): void
    {
        $lexer = new Lexer();
        $result = $lexer->extractBody($text);

        if ($extract === true) {
            $extract = $text;
        }

        static::assertEquals($extract, $result);
    }

    /**
     * @test
     */
    public function test_extractBody_noBodyTags(): void
    {
        $this->assertExtractBody('<b>Bold</b>');
    }

    /**
     * @test
     */
    public function test_extractBody_lowercaseBodyTags(): void
    {
        $this->assertExtractBody('<html><body><b>Bold</b></body></html>', '<b>Bold</b>');
    }

    /**
     * @test
     */
    public function test_extractBody_uppercaseBodyTags(): void
    {
        $this->assertExtractBody('<HTML><BODY><B>Bold</B></BODY></HTML>', '<B>Bold</B>');
    }

    /**
     * @test
     */
    public function test_extractBody_realisticUseCase(): void
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

    /**
     * @test
     */
    public function test_extractBody_bodyWithAttributes(): void
    {
        $this->assertExtractBody('<html><body bgcolor="#F00"><b>Bold</b></body></html>', '<b>Bold</b>');
    }

    /**
     * @test
     */
    public function test_extractBody_preserveUnclosedBody(): void
    {
        $this->assertExtractBody('<body>asdf'); // not closed, don't accept
    }

    /**
     * @test
     */
    public function test_extractBody_useLastBody(): void
    {
        $this->assertExtractBody('<body>foo</body>bar</body>', 'foo</body>bar');
    }

    /**
     * @test
     */
    public function test_extractBody_ignoreCommented(): void
    {
        $this->assertExtractBody('$<!-- <body>foo</body> -->^');
    }

    /**
     * @test
     */
    public function test_extractBody_butCanStillWork(): void
    {
        $this->assertExtractBody('<!-- b --><body>a</body>', 'a');
    }

    // HTMLPurifier_Lexer->tokenizeHTML() --------------------------------------

    /**
     * @param       $input
     * @param       $expect
     * @param array $alt_expect
     */
    public function assertTokenization($input, $expect, $alt_expect = []): void
    {
        $lexers = [];
        $lexers['DirectLex']  = new DirectLex();
        if (class_exists('DOMDocument')) {
            $lexers['DOMLex'] = new DOMLex();
            $lexers['PH5P']   = new _PH5P();
        }

        foreach ($lexers as $name => $lexer) {
            $result = $lexer->tokenizeHTML($input, $this->config, $this->context);
            if (isset($alt_expect[$name])) {
                if ($alt_expect[$name] === false) {
                    continue;
                }
                $t_expect = $alt_expect[$name];
                static::assertEquals($result, $alt_expect[$name], "$name: %s");
            } else {
                $t_expect = $expect;
                static::assertEquals($result, $expect, "$name: %s");
            }

            if ($t_expect != $result) {
                static::printTokens($result);
            }
        }
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_emptyInput(): void
    {
        $this->assertTokenization('', []);
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_plainText(): void
    {
        $this->assertTokenization(
            'This is regular text.',
            [
                new Text('This is regular text.')
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_textAndTags(): void
    {
        $this->assertTokenization(
            'This is <b>bold</b> text',
            [
                new Text('This is '),
                new Start('b', []),
                new Text('bold'),
                new End('b'),
                new Text(' text'),
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_normalizeCase(): void
    {
        $this->assertTokenization(
            '<DIV>Totally rad dude. <b>asdf</b></div>',
            [
                new Start('DIV', []),
                new Text('Totally rad dude. '),
                new Start('b', []),
                new Text('asdf'),
                new End('b'),
                new End('div'),
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_notWellFormed(): void
    {
        $this->assertTokenization(
            '<asdf></asdf><d></d><poOloka><poolasdf><ds></asdf></ASDF>',
            [
                new Start('asdf'),
                new End('asdf'),
                new Start('d'),
                new End('d'),
                new Start('poOloka'),
                new Start('poolasdf'),
                new Start('ds'),
                new End('asdf'),
                new End('ASDF'),
            ],
            [
                'DOMLex' => $alt = [
                    new EmptyToken('asdf'),
                    new EmptyToken('d'),
                    new Start('pooloka'),
                    new Start('poolasdf'),
                    new EmptyToken('ds'),
                    new End('poolasdf'),
                    new End('pooloka'),
                ],
                // 20140831: Weird, but whatever...
                'PH5P' => [new EmptyToken('asdf')],
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_whitespaceInTag(): void
    {
        $this->assertTokenization(
            '<a'."\t".'href="foobar.php"'."\n".'title="foo!">Link to <b id="asdf">foobar</b></a>',
            [
                new Start('a', ['href'=>'foobar.php','title'=>'foo!']),
                new Text('Link to '),
                new Start('b', ['id'=>'asdf']),
                new Text('foobar'),
                new End('b'),
                new End('a'),
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_singleAttribute(): void
    {
        $this->assertTokenization(
            '<br style="&amp;" />',
            [
                new EmptyToken('br', ['style' => '&'])
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_emptyTag(): void
    {
        $this->assertTokenization(
            '<br />',
            [new EmptyToken('br')]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_comment(): void
    {
        $this->assertTokenization(
            '<!-- Comment -->',
            [new Comment(' Comment ')]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_malformedComment(): void
    {
        $this->assertTokenization(
            '<!-- not so well formed --->',
            [new Comment(' not so well formed -')]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_unterminatedTag(): void
    {
        $this->assertTokenization(
            '<a href=""',
            [new Text('<a href=""')],
            [
                // I like our behavior better, but it's non-standard
                'DOMLex'   => [new EmptyToken('a', ['href'=>''])],
                'PH5P' => false, // total barfing, grabs scaffolding too
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_specialEntities(): void
    {
        $this->assertTokenization(
            '&lt;b&gt;',
            [
                new Text('<b>')
            ],
            [
                // some parsers will separate entities out
                'PH5P' => [
                    new Text('<'),
                    new Text('b'),
                    new Text('>'),
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_earlyQuote(): void
    {
        $this->assertTokenization(
            '<a "=>',
            [new EmptyToken('a')],
            [
                // we barf on this input
                'DirectLex' => [
                    new Start('a', ['"' => ''])
                ],
                'PH5P' => false, // behavior varies; handle this personally
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_earlyQuote_PH5P(): void
    {
        if (!class_exists('DOMDocument')) {
            static::markTestSkipped("DOMDocument does not exist");
        }

        $lexer = new _PH5P();
        $result = $lexer->tokenizeHTML('<a "=>', $this->config, $this->context);
        if ($this->context->get('PH5PError', true)) {
            $this->assertEquals([
                new Start('a', ['"' => ''])
            ], $result);
        } else {
            $this->assertEquals([
                new EmptyToken('a', ['"' => ''])
            ], $result);
        }
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_unescapedQuote(): void
    {
        $this->assertTokenization(
            '"',
            [new Text('"')]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_escapedQuote(): void
    {
        $this->assertTokenization(
            '&quot;',
            [new Text('"')]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_cdata(): void
    {
        $this->assertTokenization(
            '<![CDATA[You <b>can&#39;t</b> get me!]]>',
            [new Text('You <b>can&#39;t</b> get me!')],
            [
                'PH5P' =>  [
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
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_characterEntity(): void
    {
        $this->assertTokenization(
            '&theta;',
            [new Text("\xCE\xB8")]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_characterEntityInCDATA(): void
    {
        $this->assertTokenization(
            '<![CDATA[&rarr;]]>',
            [new Text("&rarr;")],
            [
                'PH5P' => [
                    new Text('&'),
                    new Text('rarr;'),
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_entityInAttribute(): void
    {
        $this->assertTokenization(
            '<a href="index.php?title=foo&amp;id=bar">Link</a>',
            [
                new Start('a', ['href' => 'index.php?title=foo&id=bar']),
                new Text('Link'),
                new End('a'),
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_preserveUTF8(): void
    {
        $this->assertTokenization(
            "\xCE\xB8",
            [new Text("\xCE\xB8")]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_specialEntityInAttribute(): void
    {
        $this->assertTokenization(
            '<br test="x &lt; 6" />',
            [new EmptyToken('br', ['test' => 'x < 6'])]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_emoticonProtection(): void
    {
        $this->assertTokenization(
            '<b>Whoa! <3 That\'s not good >.></b>',
            [
                new Start('b'),
                new Text('Whoa! '),
                new Text('<'),
                new Text('3 That\'s not good >.>'),
                new End('b')
            ],
            [
                // text is absorbed together
                'DOMLex' => [
                    new Start('b'),
                    new Text('Whoa! <3 That\'s not good >.>'),
                    new End('b'),
                ],
                'PH5P' => [ // interesting grouping
                    new Start('b'),
                    new Text('Whoa! '),
                    new Text('<'),
                    new Text('3 That\'s not good >.>'),
                    new End('b'),
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_commentWithFunkyChars(): void
    {
        $this->assertTokenization(
            '<!-- This >< comment --><br />',
            [
                new Comment(' This >< comment '),
                new EmptyToken('br'),
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_unterminatedComment(): void
    {
        $this->assertTokenization(
            '<!-- This >< comment',
            [new Comment(' This >< comment')],
            [
                'DOMLex'   => false,
                'PH5P'     => false,
            ]
        );
    }

    public function test_tokenizeHTML_scriptCDATAContents(): void
    {
        $this->config->set('HTML.Trusted', true);
        $this->assertTokenization(
            'Foo: <script>alert("<foo>");</script>',
            [
                new Text('Foo: '),
                new Start('script'),
                new Text('alert("<foo>");'),
                new End('script'),
            ],
            [
                // PH5P, for some reason, bubbles the script to <head>
                'PH5P' => false,
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_entitiesInComment(): void
    {
        $this->assertTokenization(
            '<!-- This comment < &lt; & -->',
            [new Comment(' This comment < &lt; & ')]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_attributeWithSpecialCharacters(): void
    {
        $this->assertTokenization(
            '<a href="><>">',
            [new EmptyToken('a', ['href' => '><>'])],
            [
                'DirectLex' => [
                    new Start('a', ['href' => '']),
                    new Text('<'),
                    new Text('">'),
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_emptyTagWithSlashInAttribute(): void
    {
        $this->assertTokenization(
            '<param name="src" value="http://example.com/video.wmv" />',
            [new EmptyToken('param', ['name' => 'src', 'value' => 'http://example.com/video.wmv'])]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_style(): void
    {
        $extra = [
            // PH5P doesn't seem to like style tags
            'PH5P' => false,
            // DirectLex defers to RemoveForeignElements for textification
            'DirectLex' => [
                new Start('style', ['type' => 'text/css']),
                new Comment("\ndiv {}\n"),
                new End('style'),
            ],
        ];

        if (!\defined('LIBXML_VERSION')) {
            // LIBXML_VERSION is missing in early versions of PHP
            // prior to 1.30 of php-src/ext/libxml/libxml.c (version-wise,
            // this translates to 5.0.x. In such cases, punt the test entirely.
            static::markTestSkipped('LIBXML_VERSION is missing');
        } elseif (LIBXML_VERSION < 20628) {
            // libxml's behavior is wrong prior to this version, so make
            // appropriate accomodations
            $extra['DOMLex'] = $extra['DirectLex'];
        }
        $this->assertTokenization(
            '<style type="text/css"><!--
div {}
--></style>',
            [
                new Start('style', ['type' => 'text/css']),
                new Text("\ndiv {}\n"),
                new End('style'),
            ],
            $extra
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_tagWithAtSignAndExtraGt(): void
    {
        $alt_expect = [
            // Technically this is invalid, but it won't be a
            // problem with invalid element removal; also, this
            // mimics Mozilla's parsing of the tag.
            new Start('a@'),
            new Text('>'),
        ];

        $this->assertTokenization(
            '<a@>>',
            [
                new Start('a'),
                new Text('>'),
                new End('a'),
            ],
            [
                'DirectLex' => $alt_expect,
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_emoticonHeart(): void
    {
        $this->assertTokenization(
            '<br /><3<br />',
            [
                new EmptyToken('br'),
                new Text('<'),
                new Text('3'),
                new EmptyToken('br'),
            ],
            [
                'DOMLex' => [
                    new EmptyToken('br'),
                    new Text('<3'),
                    new EmptyToken('br'),
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_emoticonShiftyEyes(): void
    {
        $this->assertTokenization(
            '<b><<</b>',
            [
                new Start('b'),
                new Text('<'),
                new Text('<'),
                new End('b'),
            ],
            [
                'DOMLex' => [
                    new Start('b'),
                    new Text('<<'),
                    new End('b'),
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_eon1996(): void
    {
        $this->assertTokenization(
            '< <b>test</b>',
            [
                new Text('<'),
                new Text(' '),
                new Start('b'),
                new Text('test'),
                new End('b'),
            ],
            [
                'DOMLex' => [
                    new Text('< '),
                    new Start('b'),
                    new Text('test'),
                    new End('b'),
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_bodyInCDATA(): void
    {
        $alt_tokens = [
            new Text('<'),
            new Text('body'),
            new Text('>'),
            new Text('Foo'),
            new Text('<'),
            new Text('/body'),
            new Text('>'),
        ];

        $this->assertTokenization(
            '<![CDATA[<body>Foo</body>]]>',
            [
                new Text('<body>Foo</body>'),
            ],
            [
                'PH5P' => $alt_tokens,
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_(): void
    {
        $this->assertTokenization(
            '<a><img /></a>',
            [
                new Start('a'),
                new EmptyToken('img'),
                new End('a'),
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_ignoreIECondComment(): void
    {
        $this->assertTokenization(
            '<!--[if IE]>foo<a>bar<!-- baz --><![endif]-->',
            []
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_removeProcessingInstruction(): void
    {
        $this->config->set('Core.RemoveProcessingInstructions', true);
        $this->assertTokenization(
            '<?xml blah blah ?>',
            []
        );
    }

    public function test_tokenizeHTML_removeNewline(): void
    {
        $this->config->set('Core.NormalizeNewlines', true);
        $this->assertTokenization(
            "plain\rtext\r\n",
            [
                new Text("plain\ntext\n")
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_noRemoveNewline(): void
    {
        $this->config->set('Core.NormalizeNewlines', false);
        $this->assertTokenization(
            "plain\rtext\r\n",
            [
                new Text("plain\rtext\r\n")
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_conditionalCommentUngreedy(): void
    {
        $this->assertTokenization(
            '<!--[if gte mso 9]>a<![endif]-->b<!--[if gte mso 9]>c<![endif]-->',
            [
                new Text("b")
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_imgTag(): void
    {
        $start = [
            new Start('img',
                [
                    'src' => 'img_11775.jpg',
                    'alt' => '[Img #11775]',
                    'id' => 'EMBEDDED_IMG_11775',
                ]
            )
        ];

        $this->assertTokenization(
            '<img src="img_11775.jpg" alt="[Img #11775]" id="EMBEDDED_IMG_11775" >',
            [
                new EmptyToken('img',
                    [
                        'src' => 'img_11775.jpg',
                        'alt' => '[Img #11775]',
                        'id' => 'EMBEDDED_IMG_11775',
                    ]
                )
            ],
            [
                'DirectLex' => $start,
            ]
        );
    }

    /**
     * @test
     */
    public function test_tokenizeHTML_prematureDivClose(): void
    {
        $this->assertTokenization(
            '</div>dont<b>die</b>',
            [
                new End('div'),
                new Text('dont'),
                new Start('b'),
                new Text('die'),
                new End('b'),
            ],
            [
                'DOMLex' => $alt = [
                    new Text('dont'),
                    new Start('b'),
                    new Text('die'),
                    new End('b')
                ],
                'PH5P' => $alt
            ]
        );
    }
}
