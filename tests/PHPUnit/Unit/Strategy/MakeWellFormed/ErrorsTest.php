<?php
declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed;

use HTMLPurifier\Tests\Unit\Strategy\ErrorsTestCase;
use HTMLPurifier\Strategy;
use HTMLPurifier_Strategy_MakeWellFormed;
use HTMLPurifier\Token\End;
use HTMLPurifier\Token\Start;
use Mockery;

/**
 * Class ErrorsTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed
 */
class ErrorsTest extends ErrorsTestCase
{
    /**
     * @test
     */
    public function testUnnecessaryEndTagRemoved(): void
    {
        $this->expectErrorCollection(E_WARNING, 'Strategy_MakeWellFormed: Unnecessary end tag removed');
        $this->expectContext('CurrentToken', new End('b', [], 1, 0));
        $this->invoke('</b>');
    }

    /**
     * @test
     */
    public function testUnnecessaryEndTagToText(): void
    {
        $this->config->set('Core.EscapeInvalidTags', true);
        $this->expectErrorCollection(E_WARNING, 'Strategy_MakeWellFormed: Unnecessary end tag to text');
        $this->expectContext('CurrentToken', new End('b', [], 1, 0));
        $this->invoke('</b>');
    }

    /**
     * @test
     */
    public function testTagAutoclose(): void
    {
        $this->markAsRisky();
        $this->expectErrorCollection(E_NOTICE, 'Strategy_MakeWellFormed: Tag auto closed', Mockery::any());
//        $this->expectErrorCollection(E_NOTICE, 'Strategy_MakeWellFormed: Tag auto closed', new HTMLPurifier\Token\HTMLPurifier_Token_Start('p', [], 1, 0));
        $this->expectContext('CurrentToken', new Start('div', [], 1, 6));
        $this->invoke('<p>Foo<div>Bar</div>');
    }

    /**
     * @test
     */
    public function testTagCarryOver(): void
    {
        $this->markAsRisky();
        $b = new Start('b', [], 1, 0);
        $this->expectErrorCollection(E_NOTICE, 'Strategy_MakeWellFormed: Tag carryover', Mockery::any());
//        $this->expectErrorCollection(E_NOTICE, 'Strategy_MakeWellFormed: Tag carryover', $b);
        $this->expectContext('CurrentToken', new Start('div', [], 1, 6));
        $this->invoke('<b>Foo<div>Bar</div>');
    }

    /**
     * @test
     */
    public function testStrayEndTagRemoved(): void
    {
        $this->expectErrorCollection(E_WARNING, 'Strategy_MakeWellFormed: Stray end tag removed');
        $this->expectContext('CurrentToken', new End('b', [], 1, 3));
        $this->invoke('<i></b></i>');
    }

    /**
     * @test
     */
    public function testStrayEndTagToText(): void
    {
        $this->config->set('Core.EscapeInvalidTags', true);
        $this->expectErrorCollection(E_WARNING, 'Strategy_MakeWellFormed: Stray end tag to text');
        $this->expectContext('CurrentToken', new End('b', [], 1, 3));
        $this->invoke('<i></b></i>');
    }

    /**
     * @test
     */
    public function testTagClosedByElementEnd(): void
    {
        $this->expectErrorCollection(E_NOTICE, 'Strategy_MakeWellFormed: Tag closed by element end', Mockery::any());
//        $this->expectErrorCollection(E_NOTICE, 'Strategy_MakeWellFormed: Tag closed by element end', new HTMLPurifier\Token\HTMLPurifier_Token_Start('b', [], 1, 3));
        $this->expectContext('CurrentToken', new End('i', [], 1, 12));
        $this->invoke('<i><b>Foobar</i>');
    }

    /**
     * @test
     */
    public function testTagClosedByDocumentEnd(): void
    {
        $this->markAsRisky();
        $this->expectErrorCollection(E_NOTICE, 'Strategy_MakeWellFormed: Tag closed by document end', Mockery::any());
//        $this->expectErrorCollection(E_NOTICE, 'Strategy_MakeWellFormed: Tag closed by document end', new HTMLPurifier\Token\HTMLPurifier_Token_Start('b', [], 1, 0));
        $this->invoke('<b>Foobar');
    }

    /**
     * @return \HTMLPurifier\Strategy
     */
    protected function getStrategy(): Strategy
    {
        return new HTMLPurifier_Strategy_MakeWellFormed();
    }
}
