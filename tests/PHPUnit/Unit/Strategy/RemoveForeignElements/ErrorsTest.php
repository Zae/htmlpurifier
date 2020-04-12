<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\RemoveForeignElements;

use HTMLPurifier\Tests\Unit\Strategy\ErrorsTestCase;
use HTMLPurifier\Strategy;
use HTMLPurifier_Strategy_RemoveForeignElements;
use HTMLPurifier_Token_Comment;
use HTMLPurifier_Token_Empty;
use HTMLPurifier\Token\Start;

/**
 * Class ErrorsTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\RemoveForeignElements
 */
class ErrorsTest extends ErrorsTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->config->set('HTML.TidyLevel', 'heavy');
    }

    /**
     * @test
     */
    public function testTagTransform(): void
    {
        $this->expectErrorCollection(E_NOTICE, 'Strategy_RemoveForeignElements: Tag transform', 'center');
        $this->expectContext('CurrentToken', new Start('div', ['style' => 'text-align:center;'], 1));
        $this->invoke('<center>');
    }

    /**
     * @test
     */
    public function testMissingRequiredAttr(): void
    {
        // a little fragile, since img has two required attributes
        $this->expectErrorCollection(E_ERROR, 'Strategy_RemoveForeignElements: Missing required attribute', 'alt');
        $this->expectContext('CurrentToken', new HTMLPurifier_Token_Empty('img', [], 1));
        $this->invoke('<img />');
    }

    /**
     * @test
     */
    public function testForeignElementToText(): void
    {
        $this->config->set('Core.EscapeInvalidTags', true);
        $this->expectErrorCollection(E_WARNING, 'Strategy_RemoveForeignElements: Foreign element to text');
        $this->expectContext('CurrentToken', new Start('invalid', [], 1));
        $this->invoke('<invalid>');
    }

    /**
     * @test
     */
    public function testForeignElementRemoved(): void
    {
        // uses $CurrentToken.Serialized
        $this->expectErrorCollection(E_ERROR, 'Strategy_RemoveForeignElements: Foreign element removed');
        $this->expectContext('CurrentToken', new Start('invalid', [], 1));
        $this->invoke('<invalid>');
    }

    /**
     * @test
     */
    public function testCommentRemoved(): void
    {
        $this->expectErrorCollection(E_NOTICE, 'Strategy_RemoveForeignElements: Comment removed');
        $this->expectContext('CurrentToken', new HTMLPurifier_Token_Comment(' test ', 1));
        $this->invoke('<!-- test -->');
    }

    /**
     * @test
     */
    public function testTrailingHyphenInCommentRemoved(): void
    {
        $this->config->set('HTML.Trusted', true);
        $this->expectErrorCollection(E_NOTICE, 'Strategy_RemoveForeignElements: Trailing hyphen in comment removed');
        $this->expectContext('CurrentToken', new HTMLPurifier_Token_Comment(' test ', 1));
        $this->invoke('<!-- test ---->');
    }

    /**
     * @test
     */
    public function testDoubleHyphenInCommentRemoved(): void
    {
        $this->config->set('HTML.Trusted', true);
        $this->expectErrorCollection(E_NOTICE, 'Strategy_RemoveForeignElements: Hyphens in comment collapsed');
        $this->expectContext('CurrentToken', new HTMLPurifier_Token_Comment(' test - test - test ', 1));
        $this->invoke('<!-- test --- test -- test -->');
    }

    /**
     * @test
     */
    public function testForeignMetaElementRemoved(): void
    {
        $this->markAsRisky();
        $this->collector->expects()
            ->send(E_ERROR, 'Strategy_RemoveForeignElements: Foreign meta element removed')
            ->once();

        $this->collector->expects()
            ->send(E_ERROR, 'Strategy_RemoveForeignElements: Token removed to end', 'script')
            ->once();

//        $this->collector->expectContextAt(0, 'CurrentToken', new HTMLPurifier\Token\HTMLPurifier_Token_Start('script', [], 1));
        $this->invoke('<script>asdf');
    }

    /**
     * @return Strategy
     */
    protected function getStrategy(): Strategy
    {
        return new HTMLPurifier_Strategy_RemoveForeignElements();
    }
}
