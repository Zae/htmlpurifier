<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\URIFilter;

use HTMLPurifier\URIFilter\DisableResources;

/**
 * Class DisableResourcesTest
 *
 * @package HTMLPurifier\Tests\Unit\URIFilter
 */
class DisableResourcesTest extends TestCase
{
    /**
     * @var DisableResources
     */
    protected $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new DisableResources();

        $var = true;
        $this->context->register('EmbeddedURI', $var);
    }

    /**
     * @test
     */
    public function testRemoveResource(): void
    {
        $this->assertFiltering('/foo/bar', false);
    }

    /**
     * @test
     */
    public function testPreserveRegular(): void
    {
        $this->context->destroy('EmbeddedURI'); // undo setUp
        $this->assertFiltering('/foo/bar');
    }
}
