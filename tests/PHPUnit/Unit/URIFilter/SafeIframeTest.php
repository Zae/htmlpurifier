<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\URIFilter;

use HTMLPurifier\Token;
use HTMLPurifier\URIFilter\SafeIframe;

/**
 * Class HostBlacklistTest
 *
 * @package HTMLPurifier\Tests\Unit\URIFilter
 */
class SafeIframeTest extends TestCase
{
    /**
     * @var SafeIframe
     */
    protected $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new SafeIframe();
    }

    /**
     * @test
     */
    public function testNoEmbeddedUri(): void
    {
        $this->config->set('HTML.SafeIframe', true);
        $this->assertFiltering('https://example.com', true);
    }

    /**
     * @test
     */
    public function testNoiFrame(): void
    {
        $true = true;
        $token = new Token\Start('object');
        $this->config->set('HTML.SafeIframe', true);
        $this->context->register('EmbeddedURI', $true);
        $this->context->register('CurrentToken', $token);
        $this->assertFiltering('https://example.com', true);
    }
}

