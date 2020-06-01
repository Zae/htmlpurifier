<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\HTMLModule;

/**
 * Class NofollowTest
 *
 * @package HTMLPurifier\Tests\Unit\HTMLModule
 */
class NofollowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->config->set('HTML.Nofollow', true);
        $this->config->set('Attr.AllowedRel', ["nofollow", "blah"]);
    }

    /**
     * @test
     */
    public function testNofollow(): void
    {
        $this->assertResult(
            '<a href="http://google.com">x</a><a href="http://google.com" rel="blah">a</a><a href="/local">b</a><a href="mailto:foo@example.com">c</a>',
            '<a href="http://google.com" rel="nofollow">x</a><a href="http://google.com" rel="blah nofollow">a</a><a href="/local">b</a><a href="mailto:foo@example.com">c</a>'
        );
    }

    /**
     * @test
     */
    public function testNofollowDupe(): void
    {
        $this->assertResult(
            '<a href="http://google.com" rel="nofollow">x</a><a href="http://google.com" rel="blah nofollow">a</a><a href="/local">b</a><a href="mailto:foo@example.com">c</a>'
        );
    }
}
