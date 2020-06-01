<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Injector;

/**
 * Class LinkifyTest
 *
 * @package HTMLPurifier\Tests\Unit\Injector
 */
class LinkifyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('AutoFormat.Linkify', true);
    }

    /**
     * @test
     */
    public function testLinkifyURLInRootNode(): void
    {
        $this->assertResult(
            'http://example.com',
            '<a href="http://example.com">http://example.com</a>'
        );
    }

    /**
     * @test
     */
    public function testLinkifyURLInInlineNode(): void
    {
        $this->assertResult(
            '<b>http://example.com</b>',
            '<b><a href="http://example.com">http://example.com</a></b>'
        );
    }

    /**
     * @test
     */
    public function testBasicUsageCase(): void
    {
        $this->assertResult(
            'This URL http://example.com is what you need',
            'This URL <a href="http://example.com">http://example.com</a> is what you need'
        );
    }

    /**
     * @test
     */
    public function testIgnoreURLInATag(): void
    {
        $this->assertResult(
            '<a>http://example.com/</a>'
        );
    }

    /**
     * @test
     */
    public function testNeeded(): void
    {
        $this->config->set('HTML.Allowed', 'b');
        $this->expectError();
        $this->expectErrorMessage('Cannot enable Linkify injector because a is not allowed');
        $this->assertResult('http://example.com/');
    }

    /**
     * @test
     */
    public function testExcludes(): void
    {
        $this->assertResult('<a><span>http://example.com</span></a>');
    }

    /**
     * @test
     */
    public function testRegexIsSmart(): void
    {
        $this->assertResult(
            'http://example.com/foo.',
            '<a href="http://example.com/foo">http://example.com/foo</a>.'
        );
        $this->assertResult(
            '“http://example.com/foo”',
            '“<a href="http://example.com/foo">http://example.com/foo</a>”'
        );
        $this->assertResult(
            '“http://example.com”',
            '“<a href="http://example.com">http://example.com</a>”'
        );
        $this->assertResult(
            '(http://example.com/f(o)o)',
            '(<a href="http://example.com/f(o)o">http://example.com/f(o)o</a>)'
        );
    }
}
