<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\ChildDef;

use HTMLPurifier\ChildDef\Lists;

/**
 * Class ListTest
 *
 * @package HTMLPurifier\Tests\Unit\ChildDef
 */
class ListTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Lists();
    }

    /**
     * @test
     */
    public function testEmptyInput(): void
    {
        $this->assertResult('', false);
    }

    /**
     * @test
     */
    public function testSingleLi(): void
    {
        $this->assertResult('<li />');
    }

    /**
     * @test
     */
    public function testSomeLi(): void
    {
        $this->assertResult('<li>asdf</li><li />');
    }

    /**
     * @test
     */
    public function testOlAtBeginning(): void
    {
        $this->assertResult('<ol />', '<li><ol /></li>');
    }

    /**
     * @test
     */
    public function testOlAtBeginningWithOtherJunk(): void
    {
        $this->assertResult('<ol /><li />', '<li><ol /></li><li />');
    }

    /**
     * @test
     */
    public function testOlInMiddle(): void
    {
        $this->assertResult('<li>Foo</li><ol><li>Bar</li></ol>', '<li>Foo<ol><li>Bar</li></ol></li>');
    }

    /**
     * @test
     */
    public function testMultipleOl(): void
    {
        $this->assertResult('<li /><ol /><ol />', '<li><ol /><ol /></li>');
    }

    /**
     * @test
     */
    public function testUlAtBeginning(): void
    {
        $this->assertResult('<ul />', '<li><ul /></li>');
    }

    /**
     * @test
     */
    public function testDisabledLi(): void
    {
        $this->expectError();
        $this->expectErrorMessage('Cannot allow ul/ol without allowing li');

        $def = $this->config->getHTMLDefinition();
        unset($def->info['li']);

        $this->assertResult('<li></li>', false);
    }

    /**
     * @test
     */
    public function testWhitespace(): void
    {
        $this->assertResult('<li>a</li><!--WHITESPACE--><li>b</li>');
    }

    /**
     * @test
     */
    public function testAllWhitespace(): void
    {
        $this->assertResult('<!--WHITESPASCE-->', false);
    }
}
