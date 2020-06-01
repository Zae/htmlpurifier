<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed;

use HTMLPurifier\Strategy\MakeWellFormed;
use HTMLPurifier\Tests\Unit\Strategy\TestCase;

/**
 * Class EndRewindInjectorTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed
 */
class EndRewindInjectorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->obj = new MakeWellFormed();
        $this->config->set('AutoFormat.Custom', [
            new EndRewindInjector()
        ]);
    }

    /**
     * @test
     */
    public function testBasic(): void
    {
        $this->assertResult('');
    }

    /**
     * @test
     */
    public function testFunction(): void
    {
        $this->assertResult('<span>asdf</span>', '');
    }

    /**
     * @test
     */
    public function testFailedFunction(): void
    {
        $this->assertResult('<span>asd<b>asdf</b>asdf</span>', '<span><b></b></span>');
    }

    /**
     * @test
     */
    public function testPadded(): void
    {
        $this->assertResult('<b></b><span>asdf</span><b></b>', '<b></b><b></b>');
    }

    /**
     * @test
     */
    public function testDoubled(): void
    {
        $this->config->set('AutoFormat.Custom', [
            new EndRewindInjector(),
            new EndRewindInjector(),
        ]);
        $this->assertResult('<b></b><span>asdf</span>', '<b></b>');
    }
}
