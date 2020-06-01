<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class TargetNoopenerTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class TargetNoopenerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->config->set('HTML.TargetNoreferrer', false);
        $this->config->set('HTML.TargetNoopener', true);
        $this->config->set('Attr.AllowedFrameTargets', '_blank');
    }

    /**
     * @test
     */
    public function testNoreferrer(): void
    {
        $this->assertResult(
            '<a href="http://google.com" target="_blank">x</a>',
            '<a href="http://google.com" target="_blank" rel="noopener">x</a>'
        );
    }

    /**
     * @test
     */
    public function testNoreferrerNoDupe(): void
    {
        $this->config->set('Attr.AllowedRel', 'noopener');
        $this->assertResult(
            '<a href="http://google.com" target="_blank" rel="noopener">x</a>',
            '<a href="http://google.com" target="_blank" rel="noopener">x</a>'
        );
    }

    /**
     * @test
     */
    public function testTargetBlankNoreferrer(): void
    {
        $this->config->set('HTML.TargetBlank', true);
        $this->assertResult(
            '<a href="http://google.com">x</a>',
            '<a href="http://google.com" target="_blank" rel="noopener">x</a>'
        );
    }

    /**
     * @test
     */
    public function testNoTarget(): void
    {
        $this->assertResult(
            '<a href="http://google.com">x</a>',
            '<a href="http://google.com">x</a>'
        );
    }
}
