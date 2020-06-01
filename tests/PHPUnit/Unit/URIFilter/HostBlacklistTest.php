<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\URIFilter;

use HTMLPurifier\URIFilter\HostBlacklist;

/**
 * Class HostBlacklistTest
 *
 * @package HTMLPurifier\Tests\Unit\URIFilter
 */
class HostBlacklistTest extends TestCase
{
    /**
     * @var HostBlacklist
     */
    protected $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new HostBlacklist();
    }

    /**
     * @test
     */
    public function testRejectBlacklistedHost(): void
    {
        $this->config->set('URI.HostBlacklist', 'example.com');
        $this->assertFiltering('http://example.com', false);
    }

    /**
     * @test
     */
    public function testRejectBlacklistedHostThoughNotTrue(): void
    {
        // maybe this behavior should change
        $this->config->set('URI.HostBlacklist', 'example.com');
        $this->assertFiltering('http://example.comcast.com', false);
    }

    /**
     * @test
     */
    public function testPreserveNonBlacklistedHost(): void
    {
        $this->config->set('URI.HostBlacklist', 'example.com');
        $this->assertFiltering('http://google.com');
    }
}
