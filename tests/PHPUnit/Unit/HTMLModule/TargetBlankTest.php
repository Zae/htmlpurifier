<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class TargetBlankTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class TargetBlankTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('HTML.TargetBlank', true);
    }

    /**
     * @test
     */
    public function testTargetBlank(): void
    {
        $this->assertResult(
            '<a href="http://google.com">a</a><a href="/local">b</a><a href="mailto:foo@example.com">c</a>',
            '<a href="http://google.com" target="_blank" rel="noreferrer noopener">a</a><a href="/local">b</a><a href="mailto:foo@example.com">c</a>'
        );
    }

    /**
     * @test
     */
    public function testTargetBlankNoDupe(): void
    {
        $this->assertResult(
            '<a href="http://google.com" target="_blank">a</a>',
            '<a href="http://google.com" target="_blank" rel="noreferrer noopener">a</a>'
        );
    }
}
