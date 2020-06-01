<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed;

use HTMLPurifier\Strategy\MakeWellFormed;
use HTMLPurifier\Tests\Unit\Strategy\TestCase;

/**
 * Class SkipInjectorTest
 *
 * @package HTMLPurifier\Tests\Unit\Strategy\MakeWellFormed
 */
class SkipInjectorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new MakeWellFormed();
        $this->config->set('AutoFormat.Custom', [
            new SkipInjector()
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
    public function testMultiply(): void
    {
        $this->assertResult('<br />', '<br /><br />');
    }

    /**
     * @test
     */
    public function testMultiplyMultiply(): void
    {
        $this->config->set('AutoFormat.Custom', [
            new SkipInjector(),
            new SkipInjector()
        ]);

        $this->assertResult('<br />', '<br /><br /><br /><br />');
    }
}
