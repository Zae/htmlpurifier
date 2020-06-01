<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy;

use HTMLPurifier\Strategy\Core;

/**
 * Class CoreTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy
 */
class CoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new Core();
    }

    /**
     * @test
     */
    public function testBlankInput(): void
    {
        $this->assertResult('');
    }

    /**
     * @test
     */
    public function testMakeWellFormed(): void
    {
        $this->assertResult(
            '<b>Make well formed.',
            '<b>Make well formed.</b>'
        );
    }

    /**
     * @test
     */
    public function testFixNesting(): void
    {
        $this->assertResult(
            '<b><div>Fix nesting.</div></b>',
            '<b></b><div><b>Fix nesting.</b></div><b></b>'
        );
    }

    /**
     * @test
     */
    public function testRemoveForeignElements(): void
    {
        $this->assertResult(
            '<asdf>Foreign element removal.</asdf>',
            'Foreign element removal.'
        );
    }

    /**
     * @test
     */
    public function testFirstThree(): void
    {
        $this->assertResult(
            '<foo><b><div>All three.</div></b>',
            '<b></b><div><b>All three.</b></div><b></b>'
        );
    }
}
