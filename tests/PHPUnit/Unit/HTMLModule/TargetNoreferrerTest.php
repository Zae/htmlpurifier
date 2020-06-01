<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class TargetNoreferrerTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class TargetNoreferrerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->config->set('HTML.TargetNoreferrer', true);
        $this->config->set('HTML.TargetNoopener', false);
        $this->config->set('Attr.AllowedFrameTargets', '_blank');
    }

    /**
     * @test
     */
    public function testNoreferrer(): void
    {
        $this->assertResult(
            '<a href="http://google.com" target="_blank">x</a>',
            '<a href="http://google.com" target="_blank" rel="noreferrer">x</a>'
        );
    }

    /**
     * @test
     */
    public function testNoreferrerNoDupe(): void
    {
        $this->config->set('Attr.AllowedRel', 'noreferrer');
        $this->assertResult(
            '<a href="http://google.com" target="_blank" rel="noreferrer">x</a>',
            '<a href="http://google.com" target="_blank" rel="noreferrer">x</a>'
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
            '<a href="http://google.com" target="_blank" rel="noreferrer">x</a>'
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
