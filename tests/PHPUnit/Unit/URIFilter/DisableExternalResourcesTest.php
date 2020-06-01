<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\URIFilter;

use HTMLPurifier\URIFilter\DisableExternalResources;

/**
 * Class DisableExternalResourcesTest
 *
 * @package HTMLPurifier\Tests\Unit\URIFilter
 */
class DisableExternalResourcesTest extends DisableExternalTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new DisableExternalResources();

        $var = true;
        $this->context->register('EmbeddedURI', $var);
    }

    /**
     * @test
     */
    public function testPreserveWhenNotEmbedded(): void
    {
        $this->context->destroy('EmbeddedURI'); // undo setUp
        $this->assertFiltering(
            'http://example.com'
        );
    }
}
