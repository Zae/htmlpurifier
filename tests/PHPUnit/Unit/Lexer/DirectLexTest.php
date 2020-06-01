<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Lexer;

use HTMLPurifier\Config;
use HTMLPurifier\Context;
use HTMLPurifier\Lexer\DirectLex;
use HTMLPurifier\Tests\Unit\TestCase;
use HTMLPurifier\Token\EmptyToken;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use HTMLPurifier\Token\Text;

/**
 * Class DirectLexTest
 *
 * @package HTMLPurifier\Tests\Unit\Lexer
 */
class DirectLexTest extends TestCase
{
    private $DirectLex;

    protected function setUp(): void
    {
        $this->DirectLex = new DirectLex();
    }

    // internals testing

    /**
     * @test
     * @throws \HTMLPurifier\Exception
     */
    public function test_parseAttributeString(): void
    {
        $input[0] = 'href="about:blank" rel="nofollow"';
        $expect[0] = ['href' => 'about:blank', 'rel' => 'nofollow'];

        $input[1] = "href='about:blank'";
        $expect[1] = ['href' => 'about:blank'];

        // note that the single quotes aren't /really/ escaped
        $input[2] = 'onclick="javascript:alert(\'asdf\');"';
        $expect[2] = ['onclick' => "javascript:alert('asdf');"];

        $input[3] = 'selected';
        $expect[3] = ['selected' => 'selected'];

        // [INVALID]
        $input[4] = '="nokey"';
        $expect[4] = [];

        // [SIMPLE]
        $input[5] = 'color=blue';
        $expect[5] = ['color' => 'blue'];

        // [INVALID]
        $input[6] = 'href="about:blank';
        $expect[6] = ['href' => 'about:blank'];

        // [INVALID]
        $input[7] = '"=';
        $expect[7] = ['"' => ''];
        // we ought to get array()

        $input[8] = 'href ="about:blank"rel ="nofollow"';
        $expect[8] = ['href' => 'about:blank', 'rel' => 'nofollow'];

        $input[9] = 'two bool';
        $expect[9] = ['two' => 'two', 'bool' => 'bool'];

        $input[10] = 'name="input" selected';
        $expect[10] = ['name' => 'input', 'selected' => 'selected'];

        $input[11] = '=""';
        $expect[11] = [];

        $input[12] = '="" =""';
        $expect[12] = []; // tough to say, just don't throw a loop

        $input[13] = 'href="';
        $expect[13] = ['href' => ''];

        $input[14] = 'href=" <';
        $expect[14] = ['href' => ' <'];

        $config = Config::createDefault();
        $context = new Context();

        $size = \count($input);
        for ($i = 0; $i < $size; $i++) {
            $result = $this->DirectLex->parseAttributeString($input[$i], $config, $context);
            static::assertEquals($expect[$i], $result, 'Test ' . $i . ': %s');
        }
    }

    /**
     * @test
     */
    public function testLineNumbers(): void
    {
        //       .  .     .     .  .     .     .           .      .             .
        //       01234567890123 01234567890123 0123456789012345 0123456789012   012345
        $html = "<b>Line 1</b>\n<i>Line 2</i>\nStill Line 2<br\n/>Now Line 4\n\n<br />";

        $expect = [
            // line 1
            0 => new Start('b'),
            1 => new Text('Line 1'),
            2 => new End('b'),
            3 => new Text("\n"),
            // line 2
            4 => new Start('i'),
            5 => new Text('Line 2'),
            6 => new End('i'),
            7 => new Text("\nStill Line 2"),
            // line 3
            8 => new EmptyToken('br'),
            // line 4
            9 => new Text("Now Line 4\n\n"),
            // line SIX
            10 => new EmptyToken('br')
        ];

        $context = new Context();
        $config = Config::createDefault();
        $output = $this->DirectLex->tokenizeHTML($html, $config, $context);

        static::assertEquals($expect, $output);

        $context = new Context();
        $config = Config::create([
            'Core.MaintainLineNumbers' => true
        ]);

        $expect[0]->position(1, 0);
        $expect[1]->position(1, 3);
        $expect[2]->position(1, 9);
        $expect[3]->position(2, -1);
        $expect[4]->position(2, 0);
        $expect[5]->position(2, 3);
        $expect[6]->position(2, 9);
        $expect[7]->position(3, -1);
        $expect[8]->position(3, 12);
        $expect[9]->position(4, 2);
        $expect[10]->position(6, 0);

        $output = $this->DirectLex->tokenizeHTML($html, $config, $context);
        static::assertEquals($expect, $output);
    }
}
