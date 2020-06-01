<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed;

use HTMLPurifier\Strategy\MakeWellFormed;
use HTMLPurifier\Tests\Unit\Strategy\TestCase;

/**
 * Class EndInsertInjectorTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed
 */
class EndInsertInjectorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new MakeWellFormed();
        $this->config->set('AutoFormat.Custom', [
            new EndInsertInjector()
        ]);
    }

    /**
     * @test
     */
    public function testEmpty(): void
    {
        $this->assertResult('');
    }

    /**
     * @test
     */
    public function testNormal(): void
    {
        $this->assertResult('<i>Foo</i>', '<i>Foo<b>Comment</b></i>');
    }

    /**
     * @test
     */
    public function testEndOfDocumentProcessing(): void
    {
        $this->assertResult('<i>Foo', '<i>Foo<b>Comment</b></i>');
    }

    /**
     * @test
     */
    public function testDoubleEndOfDocumentProcessing(): void
    {
        $this->assertResult('<i><i>Foo', '<i><i>Foo<b>Comment</b></i><b>Comment</b></i>');
    }

    /**
     * @test
     */
    public function testEndOfNodeProcessing(): void
    {
        $this->assertResult('<div><i>Foo</div>asdf', '<div><i>Foo<b>Comment</b></i></div><i>asdf<b>Comment</b></i>');
    }

    /**
     * @test
     */
    public function testEmptyToStartEndProcessing(): void
    {
        $this->assertResult('<i />', '<i><b>Comment</b></i>');
    }

    /**
     * @test
     */
    public function testSpuriousEndTag(): void
    {
        $this->assertResult('</i>', '');
    }

    /**
     * @test
     */
    public function testLessButStillSpuriousEndTag(): void
    {
        $this->assertResult('<div></i></div>', '<div></div>');
    }
}
