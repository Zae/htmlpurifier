<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Lexer\DirectLex;

use HTMLPurifier\Tests\Unit\ErrorsTestCase;
use HTMLPurifier_Lexer_DirectLex;

/**
 * Class ErrorsTest
 *
 * @package HTMLPurifier\Tests\Unit\Lexer\DirectLex
 */
class ErrorsTest extends ErrorsTestCase
{
    /**
     * @test
     */
    public function testExtractBody(): void
    {
        $this->expectErrorCollection(E_WARNING, 'Lexer: Extracted body');
        $this->invoke('<body>foo</body>');
    }

    /**
     * @test
     */
    public function testUnclosedComment(): void
    {
        $this->expectErrorCollection(E_WARNING, 'Lexer: Unclosed comment');
        $this->expectContext('CurrentLine', 1);
        $this->invoke('<!-- >');
    }

    /**
     * @test
     */
    public function testUnescapedLt(): void
    {
        $this->expectErrorCollection(E_NOTICE, 'Lexer: Unescaped lt');
        $this->expectContext('CurrentLine', 1);
        $this->invoke('< foo>');
    }

    /**
     * @test
     */
    public function testMissingGt(): void
    {
        $this->expectErrorCollection(E_WARNING, 'Lexer: Missing gt');
        $this->expectContext('CurrentLine', 1);
        $this->invoke('<a href=""');
    }

    // these are sub-errors, will only be thrown in context of collector

    /**
     * @test
     */
    public function testMissingAttributeKey1(): void
    {
        $this->expectErrorCollection(E_ERROR, 'Lexer: Missing attribute key');
        $this->invokeAttr('=""');
    }

    /**
     * @test
     */
    public function testMissingAttributeKey2(): void
    {
        $this->expectErrorCollection(E_ERROR, 'Lexer: Missing attribute key');
        $this->invokeAttr('foo="bar" =""');
    }

    /**
     * @test
     */
    public function testMissingEndQuote(): void
    {
        $this->expectErrorCollection(E_ERROR, 'Lexer: Missing end quote');
        $this->invokeAttr('src="foo');
    }

    /**
     * @param $input
     *
     * @throws \HTMLPurifier\Exception
     */
    private function invoke($input): void
    {
        $lexer = new HTMLPurifier_Lexer_DirectLex();
        $lexer->tokenizeHTML($input, $this->config, $this->context);
    }

    /**
     * @param $input
     *
     * @throws \HTMLPurifier\Exception
     */
    private function invokeAttr($input): void
    {
        $lexer = new HTMLPurifier_Lexer_DirectLex();
        $lexer->parseAttributeString($input, $this->config, $this->context);
    }
}
