<?php
declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\FixNesting;

use HTMLPurifier\Tests\Unit\Strategy\ErrorsTestCase;
use HTMLPurifier\Strategy;
use HTMLPurifier_Strategy_FixNesting;
use HTMLPurifier\Token\Start;

/**
 * Class ErrorsTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\FixNesting
 */
class ErrorsTest extends ErrorsTestCase
{
    /**
     * @test
     */
    public function testNodeRemoved(): void
    {
        $this->expectErrorCollection(E_ERROR, 'Strategy_FixNesting: Node removed');
        $this->expectContext('CurrentToken', new Start('ul', [], 1));
        $this->invoke('<ul></ul>');
    }

    /**
     * @test
     */
    public function testNodeExcluded(): void
    {
        $this->expectErrorCollection(E_ERROR, 'Strategy_FixNesting: Node excluded');
        $this->expectContext('CurrentToken', new Start('a', [], 2));
        $this->invoke("<a>\n<a></a></a>");
    }

    /**
     * @test
     */
    public function testNodeReorganized(): void
    {
        $this->expectErrorCollection(E_WARNING, 'Strategy_FixNesting: Node reorganized');
        $this->expectContext('CurrentToken', new Start('span', [], 1));
        $this->invoke('<span>Valid<div>Invalid</div></span>');
    }

    /**
     * @test
     */
    public function testNoNodeReorganizedForEmptyNode(): void
    {
        $this->expectNoErrorCollection();
        $this->invoke('<span></span>');
    }

    /**
     * @test
     */
    public function testNodeContentsRemoved(): void
    {
        $this->expectErrorCollection(E_ERROR, 'Strategy_FixNesting: Node contents removed');
        $this->expectContext('CurrentToken', new Start('span', [], 1));
        $this->invoke('<span><div></div></span>');
    }

    /**
     * @return \HTMLPurifier\Strategy
     */
    protected function getStrategy(): Strategy
    {
        return new HTMLPurifier_Strategy_FixNesting();
    }
}
