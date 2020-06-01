<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\Injector;

/**
 * Class DisplayLinkURITest
 *
 * @package HTMLPurifier\Tests\Unit\Injector
 */
class DisplayLinkURITest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->config->set('AutoFormat.DisplayLinkURI', true);
    }

    /**
     * @test
     */
    public function testBasicLink(): void
    {
        $this->assertResult(
            '<a href="http://malware.example.com">Don\'t go here!</a>',
            '<a>Don\'t go here!</a> (http://malware.example.com)'
        );
    }

    /**
     * @test
     */
    public function testEmptyLink(): void
    {
        $this->assertResult(
            '<a>Don\'t go here!</a>',
            '<a>Don\'t go here!</a>'
        );
    }

    /**
     * @test
     */
    public function testEmptyText(): void
    {
        $this->assertResult(
            '<a href="http://malware.example.com"></a>',
            '<a></a> (http://malware.example.com)'
        );
    }
}
